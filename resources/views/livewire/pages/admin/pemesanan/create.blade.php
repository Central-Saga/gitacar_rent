<?php

use function Livewire\Volt\{layout, title, state, with, rules, uses};
use App\Models\Pemesanan;
use App\Models\Pelanggan;
use App\Models\KendaraanUnit;
use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

layout('layouts.app');
title('Buat Pemesanan Baru');

uses(WithFileUploads::class);

state([
    'pelanggan_id' => '',
    'kendaraan_unit_id' => '',
    'waktu_mulai' => '',
    'waktu_selesai' => '',
    'status_pemesanan' => 'menunggu_konfirmasi',
    'catatan' => '',
    'bukti_pembayaran' => null,

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
]);

rules([
    'pelanggan_id' => 'required|exists:pelanggans,id',
    'kendaraan_unit_id' => 'required|exists:kendaraan_units,id',
    'waktu_mulai' => 'required|date',
    'waktu_selesai' => 'required|date|after:waktu_mulai',
    'status_pemesanan' => 'required|in:menunggu_konfirmasi,disetujui,ditolak,selesai,dibatalkan',
    'catatan' => 'nullable|string',
    'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
]);

with(function () {
    return [
        'pelanggans' => Pelanggan::orderBy('nama')->get(),
        'kendaraanUnits' => KendaraanUnit::with('kendaraan')
            ->where('status_unit', 'tersedia')
            ->orderBy('nomor_plat')
            ->get(),
    ];
});

$calculatePricing = function () {
    if (!$this->kendaraan_unit_id || !$this->waktu_mulai || !$this->waktu_selesai) {
        $this->harga_per_hari = 0;
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
            // Durasi dihitung per 24 jam, minimum 1 hari
            $diffHours = $start->diffInHours($end);
            $this->durasi = max(1, (int) ceil($diffHours / 24));
            $this->harga_per_hari = $unit->kendaraan->harga_sewa_per_hari;
            $totalHargaAwal = $this->harga_per_hari * $this->durasi;

            // Terapkan diskon jika ada promo yang valid
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

    // Re-kalkulasi harga agar total_diskon dan total_harga terupdate
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

    // Set prices explicitly from calculated values to prevent manipulation
    $validated['harga_per_hari'] = $this->harga_per_hari;
    $validated['total_harga'] = $this->total_harga;
    $validated['denda_per_hari'] = $this->harga_per_hari; // snapshot denda = harga per hari
    $validated['promo_id'] = $this->promo_id;
    $validated['total_diskon'] = $this->total_diskon;

    $errorMessage = null;

    try {
        DB::transaction(function () use ($validated, &$errorMessage) {
            // Lock unit row to prevent double booking
            $unit = KendaraanUnit::where('id', $this->kendaraan_unit_id)
                ->lockForUpdate()
                ->first();

            if (!$unit || $unit->status_unit !== 'tersedia') {
                $errorMessage = 'Unit kendaraan sudah tidak tersedia.';
                return;
            }

            // Validasi ulang promo & potong kuota jika pakai promo
            if ($validated['promo_id']) {
                $promo = Promo::where('id', $validated['promo_id'])->lockForUpdate()->first();
                if (!$promo || !$promo->isValid()) {
                    $errorMessage = 'Kode promo mendadak tidak valid atau kuota habis. Silakan hapus kode promo atau gunakan kode lain.';
                    return;
                }

                // Set parameter yang akan dimasukkan
                $promo->increment('kuota_terpakai');
            }

            $pemesanan = Pemesanan::create($validated);

            if ($this->bukti_pembayaran) {
                $pemesanan->addMedia($this->bukti_pembayaran->getRealPath())
                    ->usingName($this->bukti_pembayaran->getClientOriginalName())
                    ->toMediaCollection('bukti_pembayaran');
            }

            // Set unit status based on pemesanan status
            if ($pemesanan->status_pemesanan === 'disetujui') {
                $unit->update(['status_unit' => 'disewa']);
            } else {
                // Default: menunggu_konfirmasi → dibooking
                $unit->update(['status_unit' => 'dibooking']);
            }
        });

        if ($errorMessage) {
            $this->addError('kendaraan_unit_id', $errorMessage);
            return;
        }
    } catch (\Exception $e) {
        $this->addError('kendaraan_unit_id', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        return;
    }

    $this->dispatch('swal:toast', title: 'Pemesanan berhasil dibuat!', icon: 'success');
    $this->redirectRoute('admin.pemesanan.index', navigate: true);
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.pemesanan.index') }}" wire:navigate
                    class="inline-flex items-center text-sm font-medium text-textGray hover:text-primary transition-colors mb-4">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Pemesanan Baru</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Buat data reservasi kendaraan untuk
                    pelanggan secara manual.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                <form wire:submit="save" class="space-y-8">
                    <!-- Customer info -->
                    <div x-data="{ open: true }"
                        class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-left focus:outline-none">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                    <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                Pilih Pelanggan
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                            <div>
                                <div class="relative">
                                    <select wire:model="pelanggan_id"
                                        class="block w-full px-4 py-3 pr-10 appearance-none rounded-xl bg-white border border-inputBorder text-sm focus:ring-2 focus:ring-primary focus:outline-none transition-all cursor-pointer">
                                        <option value="">-- Pilih Pelanggan --</option>
                                        @foreach($pelanggans as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->email }})</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-textGray">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('pelanggan_id') <span
                                    class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Unit & Date -->
                    <div x-data="{ open: true }"
                        class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-left focus:outline-none">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                    <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                Informasi Sewa
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-textDark mb-1">Unit Kendaraan <span
                                            class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select wire:model.live="kendaraan_unit_id"
                                            class="block w-full px-4 py-3 pr-10 appearance-none rounded-xl bg-white border border-inputBorder text-sm focus:ring-2 focus:ring-primary focus:outline-none transition-all cursor-pointer">
                                            <option value="">-- Pilih Unit (Tersedia) --</option>
                                            @foreach($kendaraanUnits as $u)
                                                <option value="{{ $u->id }}">{{ $u->nomor_plat }} -
                                                    {{ $u->kendaraan->nama_kendaraan ?? 'Unknown' }} (Rp
                                                    {{ number_format($u->kendaraan->harga_sewa_per_hari ?? 0, 0, ',', '.') }}/hari)
                                                </option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-textGray">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('kendaraan_unit_id') <span
                                    class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-textDark mb-1">Waktu Mulai
                                            <span class="text-red-500">*</span></label>
                                        <input wire:model.live="waktu_mulai" type="datetime-local"
                                            class="block w-full px-4 py-3 rounded-xl bg-white border border-inputBorder text-sm focus:ring-2 focus:ring-primary focus:outline-none transition-all">
                                        @error('waktu_mulai') <span
                                            class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-textDark mb-1">Waktu Selesai
                                            <span class="text-red-500">*</span></label>
                                        <input wire:model.live="waktu_selesai" type="datetime-local"
                                            class="block w-full px-4 py-3 rounded-xl bg-white border border-inputBorder text-sm focus:ring-2 focus:ring-primary focus:outline-none transition-all">
                                        @error('waktu_selesai') <span
                                            class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <label class="block text-sm font-semibold text-textDark mb-1">Punya Kode Promo? <span class="text-xs text-textGray font-normal">(Opsional)</span></label>
                                    <div class="flex gap-2">
                                        <input wire:model="input_kode_promo" type="text" 
                                            class="block w-full px-4 py-3 rounded-xl bg-white border border-inputBorder text-sm focus:ring-2 focus:ring-primary focus:outline-none transition-all uppercase"
                                            placeholder="Masukkan kode promo"
                                            @if($promo_id) disabled @endif>
                                            
                                        @if($promo_id)
                                            <button type="button" wire:click="removePromo" class="px-6 py-3 bg-red-100 text-red-700 hover:bg-red-200 font-bold rounded-xl transition-colors whitespace-nowrap">
                                                Hapus
                                            </button>
                                        @else
                                            <button type="button" wire:click="applyPromo" class="px-6 py-3 bg-gray-900 text-white hover:bg-gray-800 font-bold rounded-xl shadow-sm transition-colors whitespace-nowrap">
                                                Terapkan
                                            </button>
                                        @endif
                                    </div>
                                    @if($promo_error_message)
                                        <p class="text-red-500 text-xs font-medium mt-2">{{ $promo_error_message }}</p>
                                    @endif
                                    @if($promo_applied_message)
                                        <p class="text-green-600 text-xs font-medium mt-2 flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $promo_applied_message }}
                                        </p>
                                    @endif
                                </div>

                                @if($durasi > 0)
                                    <div
                                        class="p-4 bg-primaryLight/10 rounded-xl border border-primaryLight/20 mt-4">

                                        <div class="flex items-center justify-between mb-2 pb-2 border-b border-primaryLight/20">
                                            <p class="text-sm text-textGray">Harga per Hari</p>
                                            <p class="text-sm font-bold text-textDark">Rp {{ number_format($harga_per_hari, 0, ',', '.') }}</p>
                                        </div>

                                        <div class="flex items-center justify-between mb-2 pb-2 border-b border-primaryLight/20">
                                            <p class="text-sm text-textGray">Subtotal ({{ $durasi }} Hari)</p>
                                            <p class="text-sm font-bold text-textDark">Rp {{ number_format($harga_per_hari * $durasi, 0, ',', '.') }}</p>
                                        </div>

                                        @if($total_diskon > 0)
                                            <div class="flex items-center justify-between mb-2 pb-2 border-b border-primaryLight/20">
                                                <p class="text-sm text-green-600 font-medium">Potongan Promo</p>
                                                <p class="text-sm font-bold text-green-600">- Rp {{ number_format($total_diskon, 0, ',', '.') }}</p>
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between mt-2 pt-2">
                                            <p class="text-sm text-textDark font-bold">Total Harga</p>
                                            <p class="text-2xl font-black text-primary">Rp
                                                {{ number_format($total_harga, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status & Payment -->
                    <div x-data="{ open: true }"
                        class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-left focus:outline-none">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                    <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                Status & Pembayaran
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-textDark mb-1">Status Pemesanan <span
                                            class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select wire:model="status_pemesanan"
                                            class="block w-full px-4 py-3 pr-10 appearance-none rounded-xl bg-white border border-inputBorder text-sm focus:ring-2 focus:ring-primary focus:outline-none transition-all cursor-pointer">
                                            <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                                            <option value="disetujui">Disetujui (Langsung Aktif)</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-textGray">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('status_pemesanan') <span
                                    class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror

                                    <label class="block text-sm font-semibold text-textDark mt-4 mb-1">Catatan
                                        Admin</label>
                                    <textarea wire:model="catatan" rows="3"
                                        class="block w-full px-4 py-3 rounded-xl bg-white border border-inputBorder text-sm focus:ring-2 focus:ring-primary focus:outline-none transition-all"></textarea>
                                    @error('catatan') <span
                                        class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-textDark mb-1">Upload Bukti
                                        Pembayaran /
                                        DP</label>
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-white hover:bg-gray-50 transition-colors relative">
                                        <div class="space-y-1 text-center">
                                            @if ($bukti_pembayaran)
                                                <svg class="mx-auto h-12 w-12 text-primary" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="text-sm text-gray-600 font-medium">1 File dipilih</div>
                                            @else
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600 justify-center">
                                                    <label
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primaryDark focus-within:outline-none">
                                                        <span>Upload a file</span>
                                                        <input wire:model="bukti_pembayaran" type="file" class="sr-only"
                                                            accept=".jpg,.jpeg,.png,.pdf">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">PNG, JPG, PDF up to 2MB</p>
                                            @endif
                                        </div>
                                    </div>
                                    @error('bukti_pembayaran') <span
                                    class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('admin.pemesanan.index') }}" wire:navigate
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 border-none text-textDark font-bold rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-primary hover:bg-primaryDark text-white font-bold rounded-xl shadow-sm shadow-primary/30 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Pemesanan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>