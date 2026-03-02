<?php

use function Livewire\Volt\{layout, title, state, mount};
use App\Models\Kendaraan;

layout('components.layouts.landing');
title('Katalog Sewa Mobil - Gita Car Rental');

state([
    'mobils' => []
]);

mount(function () {
    $this->mobils = Kendaraan::where('jenis_kendaraan', 'mobil')->get();
});

?>

<div class="text-[#2D2D2D] bg-[#F5F6F7] min-h-screen pt-[var(--nav-h)]">
    <!-- Header Hero -->
    <section class="relative bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 lg:py-24" data-aos="fade-up">
            <h1
                class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-[#2D2D2D] leading-tight mb-6 tracking-tight">
                Pilihan Mobil <span class="text-[#2FAE9B]">Terbaik</span>
            </h1>
            <p class="text-lg md:text-xl text-[#6C757D] max-w-2xl font-light leading-relaxed">
                Temukan mobil yang tepat untuk road trip keliling Bali, perjalanan bisnis, hingga liburan bersama
                keluarga dengan armada bersih dan terawat.
            </p>
        </div>
        <div class="absolute bottom-0 right-0 opacity-10 pointer-events-none hidden md:block">
            <i class="fas fa-car-side text-[20rem]"></i>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($mobils as $mobil)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="relative h-56 overflow-hidden">
                            @if($mobil->foto)
                                <img src="{{ Storage::url($mobil->foto) }}" alt="{{ $mobil->nama_kendaraan }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-car text-4xl"></i>
                                </div>
                            @endif
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-[#2D2D2D] shadow-sm">
                                {{ ucfirst($mobil->jenis_kendaraan) }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="text-xl font-bold text-[#2D2D2D] mb-2">{{ $mobil->nama_kendaraan }}</h4>
                            <div class="flex items-center gap-4 text-sm text-[#6C757D] mb-6">
                                <span class="flex items-center"><i class="fas fa-info-circle mr-1.5 text-[#2FAE9B]"></i>
                                    Nyaman & Terawat</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-100 pt-4">
                                <div>
                                    <span class="text-xs text-[#6C757D] block">Mulai dari</span>
                                    <span class="text-lg font-bold text-[#2FAE9B]">Rp
                                        {{ number_format($mobil->harga_sewa_per_hari, 0, ',', '.') }}<span
                                            class="text-xs font-normal text-gray-500">/hari</span></span>
                                </div>
                                <a href="https://wa.me/628123929934?text=Halo%20Gita%20Car%20Rental,%20saya%20tertarik%20menyewa%20Mobil%20{{ urlencode($mobil->nama_kendaraan) }}"
                                    target="_blank"
                                    class="bg-[#2D2D2D] hover:bg-[#1a1a1a] text-white px-5 py-2 rounded-full font-medium text-sm transition flex items-center gap-2 shadow-lg">
                                    <i class="fab fa-whatsapp"></i> Sewa
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-gray-300">
                        <i class="fas fa-car text-5xl text-gray-300 mb-6 block"></i>
                        <h3 class="text-2xl font-bold text-[#2D2D2D] mb-2">Mobil Belum Tersedia</h3>
                        <p class="text-[#6C757D]">Maaf, saat ini koleksi mobil sedang tidak tersedia atau dalam perawatan.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>