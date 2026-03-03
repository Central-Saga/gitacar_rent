<?php

use function Livewire\Volt\{layout, title, state, mount};
use App\Models\Kendaraan;

layout('components.layouts.landing');
title('Katalog Sewa Motor - Gita Car Rental');

state([
    'motors' => []
]);

mount(function () {
    $this->motors = Kendaraan::where('jenis_kendaraan', 'motor')->get();
});

?>

<div class="text-[#2D2D2D] bg-[#F5F6F7] min-h-screen pt-[var(--nav-h)]">
    <!-- Header Hero -->
    <section class="relative bg-white border-b border-gray-100 overflow-hidden">
        <!-- Background Decor (Right Block for Desktop) -->
        <div
            class="absolute top-0 right-0 h-full w-[38%] bg-gradient-to-br from-[#2FAE9B] to-[#1e9987] rounded-bl-[4rem] hidden lg:block z-0">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 lg:py-32 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-8">
                <!-- Text Content -->
                <div class="w-full lg:w-1/2 text-center lg:text-left lg:pr-12 order-2 lg:order-1" data-aos="fade-right">
                    <span
                        class="inline-block py-1.5 px-4 rounded-full bg-[#EAF8F6] text-[#2FAE9B] text-xs font-bold uppercase tracking-widest mb-6 border border-[#2FAE9B]/20">
                        Gita Car Rental
                    </span>
                    <h1
                        class="text-4xl md:text-5xl lg:text-[4rem] font-extrabold text-[#2D2D2D] leading-[1.1] mb-6 tracking-tight">
                        Motor Liburan <br class="hidden lg:block"> <span
                            class="text-[#2FAE9B] lg:text-[#2FAE9B]">Gesit</span>
                    </h1>
                    <p class="text-lg md:text-xl text-[#6C757D] font-light leading-relaxed max-w-lg mx-auto lg:mx-0">
                        Bergesekan dengan angin sore Canggu! Pilih motor yang lincah untuk selap-selip anti macet atau
                        menelusuri hidden beach di Bali.
                    </p>
                </div>

                <!-- Motor Image -->
                <div class="w-full lg:w-1/2 relative order-1 lg:order-2" data-aos="fade-left">
                    <!-- Mobile Background Shape -->
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-[#2FAE9B] to-[#1e9987] rounded-3xl transform scale-y-75 translate-y-12 lg:hidden -z-10 shadow-xl opacity-90">
                    </div>

                    <img src="{{ asset('storage/img/xmax.webp') }}" alt="Koleksi Sewa Motor"
                        class="w-full lg:w-[90%] max-w-none relative z-10 drop-shadow-[0_25px_25px_rgba(0,0,0,0.25)] transform lg:-translate-x-8 lg:-mt-8 mt-4">
                </div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($motors as $motor)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="relative h-56 overflow-hidden">
                            @if($motor->foto)
                                <img src="{{ Storage::url($motor->foto) }}" alt="{{ $motor->nama_kendaraan }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-motorcycle text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h4 class="text-xl font-bold text-[#2D2D2D] mb-2">{{ $motor->nama_kendaraan }}</h4>
                            <div class="flex items-center gap-4 text-sm text-[#6C757D] mb-6">
                                <span>{{ ucfirst($motor->jenis_kendaraan) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-100 pt-4 mb-4">
                                <div>
                                    <span class="text-xs text-[#6C757D] block">Mulai dari</span>
                                    <span class="text-lg font-bold text-[#2FAE9B]">Rp
                                        {{ number_format($motor->harga_sewa_per_hari, 0, ',', '.') }}<span
                                            class="text-xs font-normal text-gray-500">/hari</span></span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('katalog.detail', $motor->id) }}" wire:navigate
                                    class="flex-1 py-2 rounded-xl font-medium text-sm text-center bg-gray-100 hover:bg-gray-200 text-[#2D2D2D] transition-colors">
                                    Lihat Detail
                                </a>
                                <a href="{{ route('booking') }}?kendaraan_id={{ $motor->id }}" wire:navigate
                                    class="flex-1 py-2 rounded-xl font-medium text-sm text-center bg-[#2FAE9B] hover:bg-[#249584] text-white transition-colors shadow-sm">
                                    Sewa
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-gray-300">
                        <i class="fas fa-motorcycle text-5xl text-gray-300 mb-6 block"></i>
                        <h3 class="text-2xl font-bold text-[#2D2D2D] mb-2">Motor Belum Tersedia</h3>
                        <p class="text-[#6C757D]">Maaf, saat ini koleksi motor sedang tidak tersedia atau dalam perawatan.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>