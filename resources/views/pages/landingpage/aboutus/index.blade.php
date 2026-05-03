<?php

use function Livewire\Volt\{layout, title};

layout('components.layouts.landing');
title('About Us - Gita Car Rental Bali');

?>

<div class="text-[#2D2D2D] bg-[#F5F6F7] min-h-screen pt-[var(--nav-h)] overflow-hidden">
    <!-- ===== HERO SECTION ===== -->
    <section class="relative bg-white border-b border-gray-100">
        <!-- Background Decor -->
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#EAF8F6] via-white to-white opacity-70">
        </div>
        <div class="absolute top-20 left-10 w-64 h-64 bg-[#2FAE9B]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 bg-[#6ED3C2]/10 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text -->
                <div data-aos="fade-right" data-aos-duration="1000">
                    <span
                        class="inline-block py-1 px-3 rounded-full bg-[#EAF8F6] text-[#2FAE9B] text-sm font-semibold mb-6 border border-[#2FAE9B]/20">
                        Kenali Kami Lebih Dekat
                    </span>
                    <h1
                        class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-[#2D2D2D] leading-tight mb-6 tracking-tight">
                        Lebih Dari Sekadar <span class="text-[#2FAE9B]">Rental Kendaraan.</span>
                    </h1>
                    <p class="text-lg text-[#6C757D] mb-8 font-light leading-relaxed max-w-xl">
                        Gita Car Rental lahir dari semangat untuk memberikan pengalaman liburan dan mobilitas yang
                        jujur, transparan, dan tanpa drama di Pulau Dewata.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('katalog.mobil') }}"
                            class="inline-flex justify-center items-center py-3 px-6 text-base font-bold text-white bg-primary-gradient hover-bg-primary-gradient rounded-full shadow-lg transition-transform transform hover:-translate-y-1">
                            Lihat Unit Kami
                        </a>
                    </div>
                </div>

                <!-- Images -->
                <div class="relative hidden lg:block" data-aos="fade-left" data-aos-duration="1000">
                    <div
                        class="absolute -inset-4 bg-primary-gradient rounded-3xl transform rotate-3 scale-105 opacity-10">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <img src="{{ asset('img/WhatsApp Image 2026-05-03 at 17.04.33.jpeg') }}"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1549317761-bd32c8ce0db2?auto=format&fit=crop&q=80&w=2070'"
                            alt="Gita Car Rental Services"
                            class="rounded-2xl w-full h-64 object-cover shadow-lg transform translate-y-8">
                        <img src="{{ asset('img/WhatsApp Image 2026-05-03 at 17.04.34.jpeg') }}"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&q=80&w=2070'"
                            alt="Happy Customers" class="rounded-2xl w-full h-64 object-cover shadow-lg z-10">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== OUR STORY / WHO WE ARE ===== -->
    <section class="py-20 bg-[#F5F6F7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white rounded-[2rem] p-6 sm:p-8 md:p-14 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8 md:gap-12 items-center relative overflow-hidden">
                <!-- Decor -->
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5">
                </div>

                <!-- Text -->
                <div class="flex-1" data-aos="fade-up">
                    <h2 class="text-primary font-bold tracking-wider uppercase text-sm mb-2">Cerita Kami</h2>
                    <h3 class="text-3xl md:text-4xl font-extrabold text-[#2D2D2D] mb-6">Membawa Anda ke Destinasi dengan
                        Nyaman</h3>
                    <div class="space-y-4 text-[#6C757D] leading-relaxed">
                        <p>
                            Berawal dari kesulitan banyak wisatawan menemukan layanan rental kendaraan yang dapat
                            diandalkan, transparan, dan memiliki unit yang terawat baik di sekitar area Canggu.
                        </p>
                        <p>
                            Kami berkomitmen untuk hadir tidak hanya sebagai penyewaan mobil dan motor, tetapi juga
                            sebagai teman perjalanan Anda. Armada kami diservis secara berkala, dibersihkan dengan
                            standar tinggi sebelum serah terima, dan selalu siap mengantar Anda menjelajahi keindahan
                            Bali.
                        </p>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-3xl font-extrabold text-[#2FAE9B] mb-1">1000+</div>
                            <div class="text-sm text-secondary font-medium">Pelanggan Bahagia</div>
                        </div>
                        <div>
                            <div class="text-3xl font-extrabold text-[#2FAE9B] mb-1">111+</div>
                            <div class="text-sm text-secondary font-medium">Unit Kendaraan</div>
                        </div>
                    </div>
                </div>

                <!-- Image block -->
                <div class="flex-1 w-full" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-lg">
                        <img src="{{ asset('img/WhatsApp Image 2026-05-03 at 17.04.35.jpeg') }}"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1549488344-9ece322bd54b?auto=format&fit=crop&q=80&w=2070'"
                            alt="Armada Gita Car Rental" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== WHY CHOOSE US (Condensed) ===== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-extrabold text-[#2D2D2D] mb-4">Misi Layanan Kami</h2>
                <p class="text-[#6C757D] text-lg">Integritas dan kepuasan pelanggan adalah inti dari setiap layanan
                    penyewaan yang kami berikan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6" data-aos="fade-up" data-aos-delay="100">
                    <div
                        class="w-16 h-16 mx-auto bg-[#EAF8F6] rounded-2xl flex items-center justify-center text-primary text-2xl mb-6 shadow-sm transform hover:-translate-y-2 transition-transform">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="text-xl font-bold text-[#2D2D2D] mb-3">Keamanan Ekstra</h4>
                    <p class="text-[#6C757D] text-sm leading-relaxed">Inspeksi menyeluruh pada setiap kendaraan (rem,
                        mesin, ban) untuk memastikan liburan Anda bebas dari mogok di jalan.</p>
                </div>

                <div class="text-center p-6" data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-16 h-16 mx-auto bg-[#EAF8F6] rounded-2xl flex items-center justify-center text-primary text-2xl mb-6 shadow-sm transform hover:-translate-y-2 transition-transform">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h4 class="text-xl font-bold text-[#2D2D2D] mb-3">Harga Transparan</h4>
                    <p class="text-[#6C757D] text-sm leading-relaxed">Tidak ada biaya tersembunyi. Apa yang Anda lihat
                        sejak penawaran adalah apa yang Anda bayar, lengkap tanpa syarat rumit.</p>
                </div>

                <div class="text-center p-6" data-aos="fade-up" data-aos-delay="300">
                    <div
                        class="w-16 h-16 mx-auto bg-[#EAF8F6] rounded-2xl flex items-center justify-center text-primary text-2xl mb-6 shadow-sm transform hover:-translate-y-2 transition-transform">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="text-xl font-bold text-[#2D2D2D] mb-3">Layanan Ramah</h4>
                    <p class="text-[#6C757D] text-sm leading-relaxed">Kami memperlakukan pelanggan layaknya teman. Tim
                        kami siap merespon keluhan atau pertanyaan Anda kapan saja.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="py-24 relative overflow-hidden bg-[#2D2D2D]">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10">
        </div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 text-center" data-aos="zoom-in">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-6">Mulai Perjalanan Anda Bersama Kami</h2>
            <p class="text-gray-400 text-lg mb-10 max-w-2xl mx-auto">Tentukan kendaraan impian Anda untuk jalan-jalan
                keliling Bali, percayakan mobilitas Anda pada Gita Car Rental.</p>
            <div class="flex justify-center gap-4">
                <a href="https://wa.me/628123929934" target="_blank"
                    class="bg-[#25D366] hover:bg-[#128C7E] text-white px-8 py-4 rounded-full font-bold text-lg transition flex items-center justify-center shadow-lg">
                    <i class="fab fa-whatsapp mr-2 text-xl"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </section>
</div>