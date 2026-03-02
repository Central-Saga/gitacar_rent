<?php

use function Livewire\Volt\{layout, title, state, with, rules, uses, mount};
use App\Models\Pemesanan;
use App\Models\KendaraanUnit;
use App\Models\Kendaraan;
use App\Models\Promo;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

layout('components.layouts.landing');
title('Booking Kendaraan - Gita Car Rental');

uses(WithFileUploads::class);

state([
    'pelanggan_id' => '',
    'kendaraan_unit_id' => '',
    'waktu_mulai' => '',
    'waktu_selesai' => '',
    'status_pemesanan' => 'menunggu_konfirmasi',
    'catatan' => '',
    'bukti_pembayaran' => null,
    'foto_ktp' => null,

    // Auto calculated
    'harga_per_hari' => 0,
    'total_harga' => 0,
    'durasi' => 0,

    // Promo
    'input_kode_promo' => '',
    'promo_id' => null,
    'total_diskon' => 0,
    'promo_applied_message' => '',
    'promo_error_message' => '',

    // UI state
    'selected_unit' => null,
    'existing_ktp_url' => null,
]);

rules([
    'kendaraan_unit_id' => 'required|exists:kendaraan_units,id',
    'waktu_mulai' => 'required|date|after_or_equal:today',
    'waktu_selesai' => 'required|date|after:waktu_mulai',
    'catatan' => 'nullable|string',
    'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
]);

mount(function ($kendaraanUnit = null) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $pelanggan = Pelanggan::where('email', $user->email)->first();

    if ($pelanggan) {
        $this->pelanggan_id = $pelanggan->id;
        // Load existing KTP if already uploaded
        if ($pelanggan->foto_ktp) {
            $this->existing_ktp_url = Storage::url($pelanggan->foto_ktp);
        }
    } else {
        $newPelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'email' => $user->email,
            'no_telp' => '',
            'alamat' => ''
        ]);
        $this->pelanggan_id = $newPelanggan->id;
    }

    if ($kendaraanUnit) {
        $unit = KendaraanUnit::with('kendaraan')->find($kendaraanUnit);
        if ($unit && $unit->status_unit === 'tersedia') {
            $this->kendaraan_unit_id = $unit->id;
            $this->selected_unit = $unit;
            $this->harga_per_hari = $unit->kendaraan->harga_sewa_per_hari ?? 0;
        }
    } else {
        $kendaraanId = request()->query('kendaraan_id');
        if ($kendaraanId) {
            $firstAvailableUnit = KendaraanUnit::with('kendaraan')
                ->where('kendaraan_id', $kendaraanId)
                ->where('status_unit', 'tersedia')
                ->first();

            if ($firstAvailableUnit) {
                $this->kendaraan_unit_id = $firstAvailableUnit->id;
                $this->selected_unit = $firstAvailableUnit;
                $this->harga_per_hari = $firstAvailableUnit->kendaraan->harga_sewa_per_hari ?? 0;
            }
        }
    }

    // Default times
    $this->waktu_mulai = now()->addDay()->setHour(9)->setMinute(0)->format('Y-m-d\TH:i');
    $this->waktu_selesai = now()->addDays(2)->setHour(9)->setMinute(0)->format('Y-m-d\TH:i');

    $this->calculatePricing();
});

with(function () {
    return [
        'kendaraanUnits' => KendaraanUnit::with('kendaraan')
            ->where('status_unit', 'tersedia')
            ->orderBy('nomor_plat')
            ->get(),
    ];
});

$calculatePricing = function () {
    if (!$this->kendaraan_unit_id || !$this->waktu_mulai || !$this->waktu_selesai) {
        $this->total_harga = 0;
        $this->durasi = 0;
        return;
    }

    try {
        $start = Carbon::parse($this->waktu_mulai);
        $end = Carbon::parse($this->waktu_selesai);

        if ($end->isBefore($start)) {
            return;
        }

        $unit = KendaraanUnit::with('kendaraan')->find($this->kendaraan_unit_id);
        if ($unit && $unit->kendaraan) {
            $this->selected_unit = $unit;
            $diffHours = $start->diffInHours($end);
            $this->durasi = max(1, (int) ceil($diffHours / 24));
            $this->harga_per_hari = $unit->kendaraan->harga_sewa_per_hari;
            $totalHargaAwal = $this->harga_per_hari * $this->durasi;

            if ($this->promo_id) {
                $promo = Promo::find($this->promo_id);
                if ($promo && $promo->isValid()) {
                    $diskon = ($totalHargaAwal * $promo->diskon_persen) / 100;
                    if ($promo->maksimal_diskon !== null && $diskon > $promo->maksimal_diskon) {
                        $diskon = $promo->maksimal_diskon;
                    }
                    $this->total_diskon = $diskon;
                } else {
                    $this->removePromo();
                }
            } else {
                $this->total_diskon = 0;
            }

            $this->total_harga = $totalHargaAwal - $this->total_diskon;
        }
    } catch (\Exception $e) {
        // parsing error
    }
};

$updatedKendaraanUnitId = function () {
    $this->calculatePricing();
};

$updatedWaktuMulai = function () {
    $this->calculatePricing();
};

$updatedWaktuSelesai = function () {
    $this->calculatePricing();
};

$applyPromo = function () {
    $this->promo_error_message = '';
    $this->promo_applied_message = '';

    if (empty(trim($this->input_kode_promo))) {
        return;
    }

    $promo = Promo::where('kode_promo', strtoupper(trim($this->input_kode_promo)))->first();

    if (!$promo) {
        $this->promo_error_message = 'Kode promo tidak ditemukan.';
        return;
    }

    if (!$promo->isValid()) {
        $this->promo_error_message = 'Kode promo tidak aktif, kadaluarsa, atau kuota habis.';
        return;
    }

    $this->promo_id = $promo->id;
    $this->promo_applied_message = "Promo {$promo->diskon_persen}% berhasil digunakan!";

    $this->calculatePricing();
};

$removePromo = function () {
    $this->promo_id = null;
    $this->input_kode_promo = '';
    $this->total_diskon = 0;
    $this->promo_applied_message = '';
    $this->promo_error_message = '';

    $this->calculatePricing();
};

$save = function () {
    $validated = $this->validate();

    if (!$this->pelanggan_id) {
        $this->addError('pelanggan_id', 'Profil pelanggan tidak ditemukan. Silakan lengkapi profil Anda.');
        return;
    }

    // Save KTP to pelanggan if uploaded
    if ($this->foto_ktp) {
        $ktpPath = $this->foto_ktp->store('ktp', 'public');
        $pelanggan = Pelanggan::find($this->pelanggan_id);
        if ($pelanggan) {
            // Delete old KTP if exists
            if ($pelanggan->foto_ktp && Storage::disk('public')->exists($pelanggan->foto_ktp)) {
                Storage::disk('public')->delete($pelanggan->foto_ktp);
            }
            $pelanggan->update(['foto_ktp' => $ktpPath]);
        }
    }

    // Set auto values
    $validated['pelanggan_id'] = $this->pelanggan_id;
    $validated['status_pemesanan'] = 'menunggu_konfirmasi';
    $validated['harga_per_hari'] = $this->harga_per_hari;
    $validated['total_harga'] = $this->total_harga;
    $validated['denda_per_hari'] = $this->harga_per_hari;
    $validated['promo_id'] = $this->promo_id;
    $validated['total_diskon'] = $this->total_diskon;

    $errorMessage = null;

    try {
        DB::transaction(function () use ($validated, &$errorMessage) {
            $unit = KendaraanUnit::where('id', $this->kendaraan_unit_id)
                ->lockForUpdate()
                ->first();

            if (!$unit || $unit->status_unit !== 'tersedia') {
                $errorMessage = 'Unit kendaraan sudah tidak tersedia. Silakan pilih unit lain.';
                return;
            }

            if ($validated['promo_id']) {
                $promo = Promo::where('id', $validated['promo_id'])->lockForUpdate()->first();
                if (!$promo || !$promo->isValid()) {
                    $errorMessage = 'Kode promo tidak valid atau kuota habis.';
                    return;
                }
                $promo->increment('kuota_terpakai');
            }

            $pemesanan = Pemesanan::create($validated);

            if ($this->bukti_pembayaran) {
                $pemesanan->addMedia($this->bukti_pembayaran->getRealPath())
                    ->usingName($this->bukti_pembayaran->getClientOriginalName())
                    ->toMediaCollection('bukti_pembayaran');
            }

            $unit->update(['status_unit' => 'dibooking']);
        });

        if ($errorMessage) {
            $this->addError('kendaraan_unit_id', $errorMessage);
            return;
        }
    } catch (\Exception $e) {
        $this->addError('kendaraan_unit_id', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        return;
    }

    session()->flash('success', 'Pemesanan berhasil dibuat! Anda dapat melihat status pesanan Anda di halaman ini.');
    $this->redirectRoute('reservasi', navigate: true);
};

?>

<div class="text-[#2D2D2D] bg-[#F5F6F7] min-h-screen pt-[var(--nav-h)] pb-20">
    <!-- Header Hero -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 lg:py-32 text-center">
            <h1 class="text-3xl md:text-5xl font-extrabold text-[#2D2D2D] leading-tight mb-4">
                Booking <span class="text-[#2FAE9B]">Kendaraan</span>
            </h1>
            <p class="text-lg text-gray-600 font-light max-w-2xl mx-auto">
                Silakan lengkapi form di bawah ini untuk mengonfirmasi pemesanan Anda.
            </p>
        </div>
    </section>

    <!-- Content Form -->
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="save" class="space-y-8">
                <!-- Vehicle Selection -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                            <i class="fas fa-car"></i>
                        </div>
                        Pilih Kendaraan
                    </h3>

                    @if($selected_unit)
                        <div
                            class="mb-6 bg-gray-50 border border-gray-200 rounded-2xl overflow-hidden flex flex-col sm:flex-row shadow-sm">
                            <div class="w-full sm:w-48 bg-gray-200 flex-shrink-0 relative">
                                @if($selected_unit->kendaraan->foto)
                                    <img src="{{ Storage::url($selected_unit->kendaraan->foto) }}"
                                        alt="{{ $selected_unit->kendaraan->nama_kendaraan }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-3xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6 flex-1 flex flex-col justify-center">
                                <h4 class="text-xl font-bold text-[#2D2D2D] mb-1">
                                    {{ $selected_unit->kendaraan->nama_kendaraan }}</h4>
                                <div class="flex items-center gap-3 text-sm text-gray-600 mb-3">
                                    <span
                                        class="bg-gray-200 px-2.5 py-1 rounded-md font-medium text-[#2D2D2D]">{{ $selected_unit->nomor_plat }}</span>
                                    <span>Tahun {{ $selected_unit->tahun }}</span>
                                </div>
                                <div class="text-[#2FAE9B] font-bold">
                                    Rp {{ number_format($selected_unit->kendaraan->harga_sewa_per_hari, 0, ',', '.') }}<span
                                        class="text-xs text-gray-500 font-normal">/hari</span>
                                </div>
                            </div>
                            <div class="p-6 flex items-center justify-center bg-gray-50 border-l border-gray-200/50">
                                <button type="button"
                                    wire:click="$set('selected_unit', null); $set('kendaraan_unit_id', '')"
                                    class="text-sm font-semibold text-[#2FAE9B] hover:text-[#258e7f] uppercase tracking-wide">
                                    Ganti Unit
                                </button>
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Kendaraan Yang Tersedia <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <select wire:model.live="kendaraan_unit_id"
                                    class="block w-full px-5 py-4 appearance-none rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-[#2FAE9B]/50 border-transparent focus:bg-white outline-none transition-all cursor-pointer">
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($kendaraanUnits as $u)
                                        <option value="{{ $u->id }}">{{ $u->nomor_plat }} -
                                            {{ $u->kendaraan->nama_kendaraan ?? 'Unknown' }} (Rp
                                            {{ number_format($u->kendaraan->harga_sewa_per_hari ?? 0, 0, ',', '.') }}/hari)
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-5 text-gray-400">
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </div>
                            </div>
                            @error('kendaraan_unit_id') <span
                            class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <!-- Rental Dates -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        Waktu Sewa
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Pengambilan <span
                                    class="text-red-500">*</span></label>
                            <input wire:model.live="waktu_mulai" type="datetime-local"
                                class="block w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-[#2FAE9B]/50 border-transparent focus:bg-white outline-none transition-all">
                            @error('waktu_mulai') <span
                            class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Pengembalian <span
                                    class="text-red-500">*</span></label>
                            <input wire:model.live="waktu_selesai" type="datetime-local"
                                class="block w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-[#2FAE9B]/50 border-transparent focus:bg-white outline-none transition-all">
                            @error('waktu_selesai') <span
                            class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Details & Promo -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

                    <!-- Left: Notes, KTP Upload, Payment Proof -->
                    <div class="space-y-8">
                        <!-- Catatan -->
                        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                            <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                                Catatan
                            </h3>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Tambahan <span
                                        class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
                                <textarea wire:model="catatan" rows="4" placeholder="Misal: Saya butuh helm 2 buah..."
                                    class="block w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-[#2FAE9B]/50 border-transparent focus:bg-white outline-none transition-all resize-none"></textarea>
                                @error('catatan') <span
                                class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Upload KTP / Passport -->
                        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100"
                            x-data="{ ktpModalOpen: false, ktpPreviewUrl: '' }">
                            <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                Foto KTP / Passport
                            </h3>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Identitas <span
                                        class="text-xs text-gray-400 font-normal">(Wajib untuk
                                        verifikasi)</span></label>

                                @if ($foto_ktp)
                                    {{-- New uploaded KTP preview --}}
                                    <div class="mt-1 rounded-xl border border-gray-200 overflow-hidden bg-white">
                                        <div @click="ktpPreviewUrl = '{{ $foto_ktp->temporaryUrl() }}'; ktpModalOpen = true"
                                            class="block hover:opacity-90 transition-opacity cursor-pointer">
                                            <img src="{{ $foto_ktp->temporaryUrl() }}" alt="Preview KTP"
                                                class="w-full object-cover max-h-48">
                                        </div>
                                        <div
                                            class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-100">
                                            <span
                                                class="text-xs text-gray-500 font-medium truncate">{{ $foto_ktp->getClientOriginalName() }}</span>
                                            <label
                                                class="cursor-pointer text-xs text-[#2FAE9B] hover:text-[#258e7f] font-semibold ml-3 whitespace-nowrap">
                                                Ganti File
                                                <input wire:model="foto_ktp" type="file" class="sr-only"
                                                    accept=".jpg,.jpeg,.png">
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i> Klik gambar untuk melihat ukuran penuh
                                    </p>
                                @elseif ($existing_ktp_url)
                                    {{-- Already uploaded KTP on file --}}
                                    <div class="mt-1 rounded-xl border border-emerald-200 overflow-hidden bg-emerald-50">
                                        <div @click="ktpPreviewUrl = '{{ $existing_ktp_url }}'; ktpModalOpen = true"
                                            class="block hover:opacity-90 transition-opacity cursor-pointer">
                                            <img src="{{ $existing_ktp_url }}" alt="KTP Tersimpan"
                                                class="w-full object-cover max-h-48">
                                        </div>
                                        <div
                                            class="flex items-center justify-between px-4 py-3 bg-emerald-50 border-t border-emerald-100">
                                            <span class="text-xs text-emerald-700 font-medium flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> KTP sudah tersimpan
                                            </span>
                                            <label
                                                class="cursor-pointer text-xs text-[#2FAE9B] hover:text-[#258e7f] font-semibold ml-3 whitespace-nowrap">
                                                Upload Ulang
                                                <input wire:model="foto_ktp" type="file" class="sr-only"
                                                    accept=".jpg,.jpeg,.png">
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i> Klik gambar untuk melihat ukuran penuh
                                    </p>
                                @else
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors relative">
                                        <div class="space-y-1 text-center">
                                            <i class="fas fa-id-card text-4xl text-gray-400 mb-3"></i>
                                            <div class="flex text-sm justify-center">
                                                <label
                                                    class="relative cursor-pointer bg-white px-3 py-1.5 rounded-md font-medium text-[#2FAE9B] hover:text-[#258e7f] border border-[#2FAE9B]/30 focus-within:outline-none">
                                                    <span>Klik untuk Upload KTP</span>
                                                    <input wire:model="foto_ktp" type="file" class="sr-only"
                                                        accept=".jpg,.jpeg,.png">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">JPG, PNG batas maksimal 2MB</p>
                                        </div>
                                    </div>
                                @endif
                                @error('foto_ktp') <span
                                class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- KTP Preview Modal --}}
                            <template x-teleport="body">
                                <div x-show="ktpModalOpen" style="display: none;"
                                    class="fixed inset-0 z-[999] flex items-center justify-center bg-black/90 backdrop-blur-sm"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    <div
                                        class="relative w-full max-w-5xl h-full flex flex-col items-center justify-center p-4">
                                        <button type="button" @click="ktpModalOpen = false"
                                            class="absolute top-6 right-6 text-white/70 hover:text-white bg-black/50 hover:bg-black/80 rounded-full p-2 transition-all cursor-pointer z-[1000]">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <img :src="ktpPreviewUrl" @click.away="ktpModalOpen = false"
                                            class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl mb-6 relative z-50">
                                        <div class="flex gap-4 relative z-50">
                                            <a :href="ktpPreviewUrl" download
                                                class="px-6 py-3 bg-[#2FAE9B] text-white font-bold rounded-xl shadow-sm hover:bg-[#258e7f] transition-colors flex items-center gap-2">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button" @click="ktpModalOpen = false"
                                                class="px-6 py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-800 border border-gray-600 transition-colors cursor-pointer">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Bukti Pembayaran -->
                        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100"
                            x-data="{ buktiModalOpen: false, buktiPreviewUrl: '' }">
                            <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                Bukti Pembayaran
                            </h3>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload File <span
                                        class="text-xs text-gray-400 font-normal">(Disarankan)</span></label>

                                @if ($bukti_pembayaran)
                                    {{-- Preview uploaded bukti --}}
                                    <div class="mt-1 rounded-xl border border-gray-200 overflow-hidden bg-white">
                                        <div @click="buktiPreviewUrl = '{{ $bukti_pembayaran->temporaryUrl() }}'; buktiModalOpen = true"
                                            class="block hover:opacity-90 transition-opacity cursor-pointer">
                                            <img src="{{ $bukti_pembayaran->temporaryUrl() }}"
                                                alt="Preview Bukti Pembayaran" class="w-full object-cover max-h-48">
                                        </div>
                                        <div
                                            class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-100">
                                            <span
                                                class="text-xs text-gray-500 font-medium truncate">{{ $bukti_pembayaran->getClientOriginalName() }}</span>
                                            <label
                                                class="cursor-pointer text-xs text-[#2FAE9B] hover:text-[#258e7f] font-semibold ml-3 whitespace-nowrap">
                                                Ganti File
                                                <input wire:model="bukti_pembayaran" type="file" class="sr-only"
                                                    accept=".jpg,.jpeg,.png,.pdf">
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i> Klik gambar untuk melihat ukuran penuh
                                    </p>
                                @else
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors relative">
                                        <div class="space-y-1 text-center">
                                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                            <div class="flex text-sm justify-center">
                                                <label
                                                    class="relative cursor-pointer bg-white px-3 py-1.5 rounded-md font-medium text-[#2FAE9B] hover:text-[#258e7f] border border-[#2FAE9B]/30 focus-within:outline-none">
                                                    <span>Klik untuk Upload</span>
                                                    <input wire:model="bukti_pembayaran" type="file" class="sr-only"
                                                        accept=".jpg,.jpeg,.png,.pdf">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">JPG, PNG, PDF batas maksimal 2MB</p>
                                        </div>
                                    </div>
                                @endif
                                @error('bukti_pembayaran') <span
                                class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Bukti Pembayaran Preview Modal --}}
                            <template x-teleport="body">
                                <div x-show="buktiModalOpen" style="display: none;"
                                    class="fixed inset-0 z-[999] flex items-center justify-center bg-black/90 backdrop-blur-sm"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    <div
                                        class="relative w-full max-w-5xl h-full flex flex-col items-center justify-center p-4">
                                        <button type="button" @click="buktiModalOpen = false"
                                            class="absolute top-6 right-6 text-white/70 hover:text-white bg-black/50 hover:bg-black/80 rounded-full p-2 transition-all cursor-pointer z-[1000]">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <img :src="buktiPreviewUrl" @click.away="buktiModalOpen = false"
                                            class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl mb-6 relative z-50">
                                        <div class="flex gap-4 relative z-50">
                                            <a :href="buktiPreviewUrl" download
                                                class="px-6 py-3 bg-[#2FAE9B] text-white font-bold rounded-xl shadow-sm hover:bg-[#258e7f] transition-colors flex items-center gap-2">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button" @click="buktiModalOpen = false"
                                                class="px-6 py-3 bg-gray-700 text-white font-bold rounded-xl hover:bg-gray-800 border border-gray-600 transition-colors cursor-pointer">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Right: Pricing & Promo Summary -->
                    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden sticky top-24">
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                Ringkasan Biaya
                            </h3>

                            <!-- Promo Box -->
                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Punya Kode Promo?</label>
                                <div class="flex gap-2">
                                    <input wire:model="input_kode_promo" type="text"
                                        class="block w-full px-4 py-3 rounded-xl bg-white border border-gray-300 text-sm focus:ring-2 focus:ring-[#2FAE9B]/50 border-transparent focus:bg-white outline-none transition-all uppercase"
                                        placeholder="Masukkan kode promo" @if($promo_id) disabled @endif>

                                    @if($promo_id)
                                        <button type="button" wire:click="removePromo"
                                            class="px-5 py-3 bg-red-100 text-red-700 hover:bg-red-200 font-bold rounded-xl transition-colors whitespace-nowrap">Hapus</button>
                                    @else
                                        <button type="button" wire:click="applyPromo"
                                            class="px-5 py-3 bg-[#2D2D2D] hover:bg-black text-white font-bold rounded-xl shadow-sm transition-colors whitespace-nowrap">Terapkan</button>
                                    @endif
                                </div>
                                @if($promo_error_message)
                                    <p class="text-red-500 text-xs font-medium mt-2"><i
                                            class="fas fa-exclamation-circle mr-1"></i> {{ $promo_error_message }}</p>
                                @endif
                                @if($promo_applied_message)
                                    <p class="text-emerald-600 text-xs font-bold mt-2"><i
                                            class="fas fa-check-circle mr-1"></i> {{ $promo_applied_message }}</p>
                                @endif
                            </div>

                            <!-- Cost Breakdown -->
                            <div class="space-y-4 pt-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 font-medium">Harga Per Hari</span>
                                    <span class="font-bold text-[#2D2D2D]">Rp
                                        {{ number_format($harga_per_hari, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 font-medium">Durasi Sewa</span>
                                    <span class="font-bold text-[#2D2D2D]">{{ $durasi }} Hari</span>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <span class="text-gray-600 font-medium">Subtotal</span>
                                    <span class="font-bold text-[#2D2D2D]">Rp
                                        {{ number_format($harga_per_hari * $durasi, 0, ',', '.') }}</span>
                                </div>

                                @if($total_diskon > 0)
                                    <div class="flex items-center justify-between">
                                        <span class="text-emerald-600 font-medium">Diskon Promo</span>
                                        <span class="font-bold text-emerald-600">- Rp
                                            {{ number_format($total_diskon, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Total Block -->
                        <div class="bg-[#2D2D2D] p-8 text-white">
                            <div class="flex items-center justify-between mb-8">
                                <span class="text-gray-300 font-medium">Total Pembayaran</span>
                                <span class="text-3xl font-black text-[#2FAE9B]">Rp
                                    {{ number_format($total_harga, 0, ',', '.') }}</span>
                            </div>

                            <button type="submit"
                                class="w-full bg-[#2FAE9B] hover:bg-[#258e7f] text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i> Selesaikan Booking
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>