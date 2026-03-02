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
    <section class="relative bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 lg:py-24" data-aos="fade-up">
            <h1
                class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-[#2D2D2D] leading-tight mb-6 tracking-tight">
                Motor Liburan <span class="text-[#2FAE9B]">Gesit</span>
            </h1>
            <p class="text-lg md:text-xl text-[#6C757D] max-w-2xl font-light leading-relaxed">
                Bergesekan dengan angin sore Canggu! Pilih motor yang lincah untuk selap-selip anti macet atau
                menelusuri hidden beach di Bali.
            </p>
        </div>
        <div class="absolute bottom-0 right-0 opacity-10 pointer-events-none hidden md:block">
            <i class="fas fa-motorcycle text-[20rem]"></i>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @forelse($motors as $motor)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="relative h-48 overflow-hidden">
                            @if($motor->foto)
                                <img src="{{ Storage::url($motor->foto) }}" alt="{{ $motor->nama_kendaraan }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-motorcycle text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h4 class="text-lg font-bold text-[#2D2D2D] mb-1">{{ $motor->nama_kendaraan }}</h4>
                            <div class="flex items-center gap-2 text-xs text-[#6C757D] mb-4">
                                <span>{{ ucfirst($motor->jenis_kendaraan) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-100 mt-2 pt-3">
                                <div>
                                    <span class="text-lg font-bold text-[#2FAE9B]">Rp
                                        {{ number_format($motor->harga_sewa_per_hari, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-500">/hr</span>
                                </div>
                                <a href="https://wa.me/628123929934?text=Halo%20Gita%20Car%20Rental,%20saya%20tertarik%20menyewa%20Motor%20{{ urlencode($motor->nama_kendaraan) }}"
                                    target="_blank"
                                    class="bg-[#F5F6F7] hover:bg-[#2FAE9B] hover:text-white text-[#2D2D2D] px-4 py-2 rounded-lg font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-sm">
                                    <i class="fab fa-whatsapp text-sm"></i> Sewa
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