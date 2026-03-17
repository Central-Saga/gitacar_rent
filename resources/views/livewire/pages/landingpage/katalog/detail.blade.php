<?php

use function Livewire\Volt\{layout, title, state, mount};
use App\Models\Kendaraan;
use App\Models\KendaraanUnit;

layout('components.layouts.landing');
title('Detail Kendaraan - Gita Car Rental');

state([
    'kendaraan' => null,
    'units' => []
]);

mount(function (Kendaraan $kendaraan) {
    $this->kendaraan = $kendaraan;
    $this->units = $kendaraan->units()->get();

    // Set dynamic page title
    title($kendaraan->nama_kendaraan . ' - Gita Car Rental');
});

?>

<div class="text-[#2D2D2D] bg-[#F5F6F7] min-h-screen pt-[var(--nav-h)] pb-20">
    <!-- Header Hero -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 lg:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Image Section -->
                <div
                    class="rounded-3xl overflow-hidden shadow-sm border border-gray-100 bg-gray-50 aspect-[4/3] flex items-center justify-center relative">
                    @if($kendaraan->foto)
                        <img src="{{ $kendaraan->foto_url }}" alt="{{ $kendaraan->nama_kendaraan }}"
                            onerror="this.onerror=null; this.src='{{ $kendaraan->placeholder_foto_url }}';"
                            class="w-full h-full object-cover">
                    @else
                        <i
                            class="fas fa-{{ $kendaraan->jenis_kendaraan == 'mobil' ? 'car' : 'motorcycle' }} text-6xl text-gray-300"></i>
                    @endif
                    <div
                        class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-bold text-[#2D2D2D] shadow-sm uppercase tracking-wider">
                        {{ $kendaraan->jenis_kendaraan }}
                    </div>
                </div>

                <!-- Info Section -->
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-[#2D2D2D] leading-tight mb-4">
                        {{ $kendaraan->nama_kendaraan }}
                    </h1>

                    <div class="text-3xl font-bold text-[#2FAE9B] mb-6">
                        Rp {{ number_format($kendaraan->harga_sewa_per_hari, 0, ',', '.') }}<span
                            class="text-base font-normal text-gray-500">/hari</span>
                    </div>

                    <div class="prose prose-gray max-w-none text-gray-600 mb-8 font-light leading-relaxed">
                        @if($kendaraan->deskripsi)
                            {!! nl2br(e($kendaraan->deskripsi)) !!}
                        @else
                            <p>Kendaraan siap menemani perjalanan Anda di Bali. Terawat, bersih, dan nyaman digunakan.</p>
                        @endif
                    </div>

                    <div class="flex gap-4">
                        <a href="#units"
                            class="bg-[#2D2D2D] hover:bg-[#1a1a1a] text-white px-8 py-4 rounded-xl font-bold text-base transition-colors shadow-lg inline-flex items-center gap-2">
                            <i class="fas fa-list-ul"></i> Lihat Unit Tersedia
                        </a>
                        <a href="{{ route('katalog.' . $kendaraan->jenis_kendaraan) }}" wire:navigate
                            class="bg-white border border-gray-200 hover:bg-gray-50 text-[#2D2D2D] px-8 py-4 rounded-xl font-bold text-base transition-colors inline-flex items-center gap-2">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Units Section -->
    <section id="units" class="py-16 scroll-mt-[var(--nav-h)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center max-w-2xl mx-auto">
                <h2 class="text-3xl font-bold text-[#2D2D2D] mb-4">Daftar Unit</h2>
                <p class="text-gray-600 font-light">Pilih unit kendaraan yang tersedia sesuai dengan kebutuhan Anda.
                    Unit yang sedang dirental atau dipesan tidak dapat dipilih.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($units as $unit)
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border {{ $unit->status_unit === 'tersedia' ? 'border-primaryLight/30 hover:shadow-md transition-shadow' : 'border-gray-100 opacity-75' }}">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="text-2xl font-bold text-[#2D2D2D] uppercase tracking-wider">
                                    {{ $unit->nomor_plat }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">Tahun: {{ $unit->tahun }}</div>
                            </div>

                            @if($unit->status_unit === 'tersedia')
                                <span
                                    class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Tersedia</span>
                            @elseif($unit->status_unit === 'dibooking')
                                <span
                                    class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Dibooking</span>
                            @else
                                <span
                                    class="bg-rose-100 text-rose-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Disewa</span>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-gray-100 mt-4">
                            @if($unit->status_unit === 'tersedia')
                                <a href="{{ route('booking', $unit->id) }}" wire:navigate
                                    class="w-full block text-center bg-[#2FAE9B] hover:bg-[#258e7f] text-white px-4 py-3 rounded-xl font-bold transition-colors shadow-sm">
                                    Pilih & Sewa
                                </a>
                            @else
                                <button disabled
                                    class="w-full bg-gray-100 text-gray-400 px-4 py-3 rounded-xl font-bold cursor-not-allowed">
                                    Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-gray-300">
                        <i class="fas fa-boxes text-5xl text-gray-300 mb-6 block"></i>
                        <h3 class="text-xl font-bold text-[#2D2D2D] mb-2">Belum Ada Unit</h3>
                        <p class="text-[#6C757D]">Kendaraan ini belum memiliki unit yang didaftarkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
