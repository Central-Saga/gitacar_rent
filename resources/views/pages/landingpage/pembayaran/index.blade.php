<?php

use function Livewire\Volt\{layout, title, state, mount, rules, uses};
use App\Models\Pemesanan;
use App\Models\KendaraanUnit;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

layout('components.layouts.landing');
title('Pembayaran - Gita Car Rental');

uses(WithFileUploads::class);

state([
    'bookingData' => null,
    'unit' => null,
    'bukti_pembayaran' => null,
]);

rules([
    'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
]);

mount(function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $this->bookingData = session('pending_booking');
    
    if (!$this->bookingData) {
        return redirect()->route('booking');
    }

    $this->unit = KendaraanUnit::with('kendaraan')->find($this->bookingData['kendaraan_unit_id']);
});

$submit = function () {
    $this->validate();

    $errorMessage = null;
    $pemesanan = null;

    try {
        DB::transaction(function () use (&$errorMessage, &$pemesanan) {
            $unit = KendaraanUnit::where('id', $this->bookingData['kendaraan_unit_id'])
                ->lockForUpdate()
                ->first();

            if (!$unit || $unit->status_unit !== 'tersedia') {
                $errorMessage = 'Unit kendaraan sudah tidak tersedia. Silakan pilih unit lain.';
                return;
            }

            if (isset($this->bookingData['promo_id']) && $this->bookingData['promo_id']) {
                $promo = Promo::where('id', $this->bookingData['promo_id'])->lockForUpdate()->first();
                if (!$promo || !$promo->isValid()) {
                    $errorMessage = 'Kode promo tidak valid atau kuota habis.';
                    return;
                }
                $promo->increment('kuota_terpakai');
            }

            $pemesanan = Pemesanan::create($this->bookingData);
            
            if ($this->bukti_pembayaran) {
                $pemesanan->addMedia($this->bukti_pembayaran->getRealPath())
                    ->usingName($this->bukti_pembayaran->getClientOriginalName())
                    ->toMediaCollection('bukti_pembayaran');
            }

            $unit->update(['status_unit' => 'dibooking']);
        });

        if ($errorMessage) {
            session()->flash('error', $errorMessage);
            $this->redirectRoute('booking', navigate: false);
            return;
        }
    } catch (\Exception $e) {
        $this->addError('bukti_pembayaran', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        return;
    }

    session()->forget('pending_booking');
    session()->flash('success', 'Pembayaran berhasil dikonfirmasi! Silakan tunggu verifikasi admin.');
    $this->redirectRoute('reservasi', navigate: false);
};

?>

<div class="text-[#2D2D2D] bg-[#F5F6F7] min-h-screen pt-[var(--nav-h)] pb-20">
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h1 class="text-3xl md:text-5xl font-extrabold text-[#2D2D2D] leading-tight mb-4">
                Detail <span class="text-[#2FAE9B]">Pembayaran</span>
            </h1>
            <p class="text-lg text-gray-600 font-light max-w-2xl mx-auto">
                Selesaikan pembayaran Anda untuk pesanan kendaraan.
            </p>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form wire:submit="submit" class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                
                <!-- Left: Rekening & Form -->
                <div class="space-y-8">
                    
                    <!-- Info Rekening -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                                <i class="fas fa-university"></i>
                            </div>
                            Transfer Bank
                        </h3>
                        <p class="text-sm text-gray-600 mb-6">Silakan transfer ke rekening berikut sesuai dengan total pembayaran Anda.</p>

                        <div class="space-y-4">
                            <!-- BCA -->
                            <div class="border border-gray-200 rounded-xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{ copied: false }">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-12 flex items-center justify-center">
                                        <img src="{{ asset('bcalogo.jpg') }}" alt="BCA" class="max-w-full max-h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Bank Central Asia</p>
                                        <p class="font-bold text-lg text-[#2D2D2D] mt-0.5 font-mono" id="bca-acc">7725010362</p>
                                        <p class="text-sm text-gray-600 mt-1">a.n. I Nyoman Rianta</p>
                                    </div>
                                </div>
                                <button type="button" @click="navigator.clipboard.writeText('7725010362'); copied = true; setTimeout(() => copied = false, 2000)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-[#2D2D2D] text-sm font-semibold rounded-lg transition border border-gray-200 flex items-center gap-2">
                                    <i class="fas bg-transparent" :class="copied ? 'fa-check text-green-500' : 'fa-copy'"></i>
                                    <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Bukti -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100"
                        x-data="{ buktiModalOpen: false, buktiPreviewUrl: '' }">
                        <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            Upload Bukti Transfer
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Upload File <span class="text-red-500">*</span></label>

                            @if ($bukti_pembayaran)
                                <div class="mt-1 rounded-xl border border-gray-200 overflow-hidden bg-white">
                                    <div @click="buktiPreviewUrl = '{{ $bukti_pembayaran->temporaryUrl() }}'; buktiModalOpen = true" class="block hover:opacity-90 transition-opacity cursor-pointer">
                                        <img src="{{ $bukti_pembayaran->temporaryUrl() }}" alt="Preview Bukti Pembayaran" class="w-full object-cover max-h-48">
                                    </div>
                                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-100">
                                        <span class="text-xs text-gray-500 font-medium truncate">{{ $bukti_pembayaran->getClientOriginalName() }}</span>
                                        <label class="cursor-pointer text-xs text-[#2FAE9B] hover:text-[#258e7f] font-semibold ml-3 whitespace-nowrap">
                                            Ganti File
                                            <input wire:model="bukti_pembayaran" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                                        </label>
                                    </div>
                                </div>
                            @else
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors relative">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <div class="flex text-sm justify-center">
                                            <label class="relative cursor-pointer bg-white px-3 py-1.5 rounded-md font-medium text-[#2FAE9B] hover:text-[#258e7f] border border-[#2FAE9B]/30 focus-within:outline-none">
                                                <span>Klik untuk Upload</span>
                                                <input wire:model="bukti_pembayaran" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">JPG, PNG, PDF batas maksimal 2MB</p>
                                    </div>
                                </div>
                            @endif
                            @error('bukti_pembayaran') <span class="text-red-500 text-xs font-medium mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Bukti Pembayaran Preview Modal --}}
                        <template x-teleport="body">
                            <div x-show="buktiModalOpen" style="display: none;"
                                class="fixed inset-0 z-[999] flex items-center justify-center bg-black/90 backdrop-blur-sm">
                                <div class="relative w-full max-w-5xl h-full flex flex-col items-center justify-center p-4">
                                    <button type="button" @click="buktiModalOpen = false" class="absolute top-6 right-6 text-white/70 hover:text-white bg-black/50 hover:bg-black/80 rounded-full p-2 transition-all cursor-pointer z-[1000]">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <img :src="buktiPreviewUrl" @click.away="buktiModalOpen = false" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl mb-6 relative z-50">
                                </div>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Right: Ringkasan Pesanan -->
                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden sticky top-24">
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-[#2D2D2D] mb-6 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#2FAE9B]/10 flex items-center justify-center border border-[#2FAE9B]/20 text-[#2FAE9B]">
                                <i class="fas fa-list-alt"></i>
                            </div>
                            Informasi Pesanan
                        </h3>

                        @if($bookingData && $unit)
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
                                    @if($unit->kendaraan->foto)
                                        <img src="{{ $unit->kendaraan->foto_url }}" alt="Mobil" class="w-20 h-16 object-cover rounded-xl border border-gray-200">
                                    @else
                                        <div class="w-20 h-16 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                                            <i class="fas fa-car"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-bold text-[#2D2D2D]">{{ $unit->kendaraan->nama_kendaraan }}</h4>
                                        <p class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded inline-block mt-1">{{ $unit->nomor_plat }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                    <div>
                                        <p class="text-gray-500 mb-1">Mulai</p>
                                        <p class="font-semibold text-[#2D2D2D]">{{ \Carbon\Carbon::parse($bookingData['waktu_mulai'])->translatedFormat('d M Y, H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 mb-1">Selesai</p>
                                        <p class="font-semibold text-[#2D2D2D]">{{ \Carbon\Carbon::parse($bookingData['waktu_selesai'])->translatedFormat('d M Y, H:i') }}</p>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-100 space-y-3 pb-4">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Durasi Sewa</span>
                                        <span class="font-bold">{{ \Carbon\Carbon::parse($bookingData['waktu_mulai'])->diffInDays(\Carbon\Carbon::parse($bookingData['waktu_selesai'])) ?: 1 }} Hari</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Harga per Hari</span>
                                        <span class="font-bold">Rp {{ number_format($bookingData['harga_per_hari'], 0, ',', '.') }}</span>
                                    </div>
                                    @if(isset($bookingData['total_diskon']) && $bookingData['total_diskon'] > 0)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-emerald-600">Diskon</span>
                                        <span class="font-bold text-emerald-600">- Rp {{ number_format($bookingData['total_diskon'], 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-[#2D2D2D] p-8 text-white">
                        <p class="text-gray-300 font-medium mb-1 text-sm">Total yang harus dibayar</p>
                        <p class="text-4xl font-black text-[#2FAE9B] mb-6">Rp {{ number_format($this->bookingData['total_harga'] ?? 0, 0, ',', '.') }}</p>
                        <button type="submit" class="w-full bg-[#2FAE9B] hover:bg-[#258e7f] text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Selesaikan Pembayaran
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </section>
</div>
