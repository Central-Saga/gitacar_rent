<?php

use function Livewire\Volt\{layout, title, state, mount};
use App\Models\Kendaraan;

layout('components.layouts.landing');
title('Gita Car Rental - Sewa Mobil & Motor di Canggu, Bali');

state([
  'mobils' => [],
  'motors' => []
]);

mount(function () {
  $this->mobils = Kendaraan::where('jenis_kendaraan', 'mobil')
    ->take(4)
    ->get();
  $this->motors = Kendaraan::where('jenis_kendaraan', 'motor')
    ->take(4)
    ->get();
});

?>
<style>
  :root {
    --nav-h: 80px;
  }
</style>

<div class="text-[#2D2D2D] bg-[#F5F6F7]">
  <!-- ===== HERO ===== -->
  <section id="hero" class="relative min-h-[90svh] pt-[var(--nav-h)] overflow-hidden flex items-center bg-white">
    <!-- BG Image with Gradient Overlay -->
    <div class="absolute inset-0">
      <img src="{{ asset('storage/img/hero_section_home.png') }}" alt="Sewa Mobil dan Motor Canggu"
        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1549488344-9ece322bd54b?q=80&w=2070&auto=format&fit=crop'"
        class="w-full h-full object-cover object-center" fetchpriority="high">
      <div class="absolute inset-0 bg-gradient-to-r from-[#2D2D2D]/90 via-[#2D2D2D]/70 to-transparent"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 lg:py-24">
      <div class="max-w-3xl" data-aos="fade-up" data-aos-duration="1000">
        <span
          class="inline-block py-1 px-3 rounded-full bg-[#2FAE9B]/20 text-[#6ED3C2] text-sm font-semibold mb-6 border border-[#2FAE9B]/30 backdrop-blur-sm">🚗
          Menyediakan Mobil & Motor Terbaik</span>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6 tracking-tight">
          Menjelajah Bali <span class="text-[#6ED3C2]">Tanpa Ribet</span> di Canggu.
        </h1>

        <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl font-light leading-relaxed">
          Temukan kemudahan, kecepatan, dan kenyamanan perjalanan Anda dengan unit bersih terawat dari Gita Car Rental.
          Pesan sekarang, jalan-jalan tenang!
        </p>

        <div class="flex flex-col sm:flex-row gap-4 mb-12">
          <a href="#katalog"
            class="flex w-full sm:w-auto justify-center items-center py-3 px-8 text-base font-bold text-white bg-gradient-to-r from-[#2FAE9B] to-[#6ED3C2] hover:from-[#248f7f] hover:to-[#5bc0b0] rounded-full shadow-[0_10px_20px_-10px_rgba(46,174,155,0.6)] transition-all transform hover:-translate-y-1">
            Lihat Kendaraan
          </a>
          <a href="https://wa.me/628123929934" target="_blank"
            class="flex w-full sm:w-auto justify-center items-center py-3 px-8 text-sm sm:text-base font-bold text-white bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 rounded-full transition-all transform hover:-translate-y-1">
            <i class="fab fa-whatsapp text-xl mr-2 text-[#25D366]"></i> Chat WhatsApp Sekarang
          </a>
        </div>

        <!-- Trust Points -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-white/20 pt-6 mt-4">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-full bg-white/10 flex flex-shrink-0 items-center justify-center text-[#6ED3C2]">
              <i class="fas fa-check-circle"></i>
            </div>
            <span class="text-white text-sm font-medium">Unit Terawat</span>
          </div>
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-full bg-white/10 flex flex-shrink-0 items-center justify-center text-[#6ED3C2]">
              <i class="fas fa-bolt"></i>
            </div>
            <span class="text-white text-sm font-medium">Proses Cepat</span>
          </div>
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-full bg-white/10 flex flex-shrink-0 items-center justify-center text-[#6ED3C2]">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <span class="text-white text-sm font-medium">Lokasi Strategis</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== WHY CHOOSE US ===== -->
  <section id="about" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
        <h2 class="text-primary font-bold tracking-wider uppercase text-sm mb-2">Keunggulan Kami</h2>
        <h3 class="text-3xl md:text-4xl font-extrabold text-[#2D2D2D] mb-4">Kenapa Memilih Gita Car Rental?</h3>
        <p class="text-[#6C757D] text-lg">Kami pastikan liburan Anda di Bali berjalan mulus, aman, dan berkesan dengan
          layanan rental motor dan mobil yang gak pakai ribet.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Poin 1 -->
        <div class="bg-[#F5F6F7] p-8 rounded-2xl transition hover:shadow-xl hover:-translate-y-2 group"
          data-aos="fade-up" data-aos-delay="100">
          <div
            class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm mb-6 text-primary text-2xl group-hover:bg-[#2FAE9B] group-hover:text-white transition">
            <i class="fas fa-star border-transparent"></i>
          </div>
          <h4 class="text-xl font-bold text-[#2D2D2D] mb-3">Unit Bersih & Terawat</h4>
          <p class="text-[#6C757D] text-sm leading-relaxed">Kebersihan dan kenyamanan adalah prioritas. Semua kendaraan
            kami diservis rutin dan dicuci bersih sebelum serah terima.</p>
        </div>

        <!-- Poin 2 -->
        <div class="bg-[#F5F6F7] p-8 rounded-2xl transition hover:shadow-xl hover:-translate-y-2 group"
          data-aos="fade-up" data-aos-delay="200">
          <div
            class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm mb-6 text-primary text-2xl group-hover:bg-[#2FAE9B] group-hover:text-white transition">
            <i class="fas fa-stopwatch"></i>
          </div>
          <h4 class="text-xl font-bold text-[#2D2D2D] mb-3">Proses Cepat & Mudah</h4>
          <p class="text-[#6C757D] text-sm leading-relaxed">Liburan tidak membuang waktu. Pesan gampang, syarat mudah,
            dan kendaraan langsung siap Anda gunakan.</p>
        </div>

        <!-- Poin 3 -->
        <div class="bg-[#F5F6F7] p-8 rounded-2xl transition hover:shadow-xl hover:-translate-y-2 group"
          data-aos="fade-up" data-aos-delay="300">
          <div
            class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm mb-6 text-primary text-2xl group-hover:bg-[#2FAE9B] group-hover:text-white transition">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <h4 class="text-xl font-bold text-[#2D2D2D] mb-3">Lokasi Strategis</h4>
          <p class="text-[#6C757D] text-sm leading-relaxed">Mudah dijangkau! Kami berada di jantung area Canggu yang
            happening, titik awal sempurna untuk menjelajah.</p>
        </div>

        <!-- Poin 4 -->
        <div class="bg-[#F5F6F7] p-8 rounded-2xl transition hover:shadow-xl hover:-translate-y-2 group"
          data-aos="fade-up" data-aos-delay="400">
          <div
            class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-sm mb-6 text-primary text-2xl group-hover:bg-[#2FAE9B] group-hover:text-white transition">
            <i class="fas fa-headset"></i>
          </div>
          <h4 class="text-xl font-bold text-[#2D2D2D] mb-3">Support Responsif</h4>
          <p class="text-[#6C757D] text-sm leading-relaxed">Butuh bantuan di jalan? Tim support kami stand-by dan cepat
            merespon melalui WhatsApp 24 jam sehari.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== KATALOG KENDARAAN ===== -->
  <section id="katalog" class="py-20 bg-[#F5F6F7] scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Sewa Mobil -->
      <div id="sewa-mobil" class="mb-24 scroll-mt-24">
        <div class="flex flex-col md:flex-row justify-between items-end mb-10" data-aos="fade-up">
          <div class="max-w-xl">
            <h2 class="text-primary font-bold tracking-wider uppercase text-sm mb-2">Gita Car Rental</h2>
            <h3 class="text-3xl md:text-4xl font-extrabold text-[#2D2D2D] mb-4">Koleksi Mobil</h3>
            <p class="text-[#6C757D]">Pilihan mobil nyaman untuk road trip keliling Bali, dari area pantai hingga
              pegunungan bersama keluarga atau teman liburan.</p>
          </div>
          <a href="{{ route('katalog.mobil') }}"
            class="mt-4 md:mt-0 inline-flex items-center text-primary font-semibold hover:text-[#248f7f] border-b-2 border-transparent hover:border-[#248f7f] transition">
            Lihat Semua Mobil <i class="fas fa-arrow-right ml-2"></i>
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          @forelse($mobils as $mobil)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group"
              data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
              <div class="relative h-48 overflow-hidden">
                @if($mobil->foto)
                  <img src="{{ $mobil->foto_url }}" alt="{{ $mobil->merk }}"
                    onerror="this.onerror=null; this.src='{{ $mobil->placeholder_foto_url }}';"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                  <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                    <i class="fas fa-car text-4xl"></i>
                  </div>
                @endif
              </div>
              <div class="p-5">
                <h4 class="text-lg font-bold text-[#2D2D2D] mb-1">{{ $mobil->nama_kendaraan }}</h4>
                <div class="text-xs text-[#6C757D] mb-4 flex gap-3">
                  <span>{{ ucfirst($mobil->jenis_kendaraan) }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                  <div>
                    <span class="text-lg font-bold text-[#2FAE9B]">Rp
                      {{ number_format($mobil->harga_sewa_per_hari, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-500">/hr</span>
                  </div>
                  <a href="https://wa.me/628123929934?text=Halo%20Gita%20Car%20Rental,%20saya%20tertarik%20menyewa%20Mobil%20{{ urlencode($mobil->nama_kendaraan) }}"
                    target="_blank"
                    class="bg-[#F5F6F7] hover:bg-[#2FAE9B] hover:text-white text-[#2D2D2D] px-4 py-2 rounded-lg font-semibold text-xs transition-colors flex items-center gap-1.5">
                    <i class="fab fa-whatsapp"></i> Sewa
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-gray-300">
              <i class="fas fa-car-side text-4xl text-gray-300 mb-4 block"></i>
              <p class="text-gray-500">Belum ada mobil yang tersedia saat ini.</p>
            </div>
          @endforelse
        </div>
      </div>

      <!-- Sewa Motor -->
      <div id="sewa-motor" class="scroll-mt-24">
        <div class="flex flex-col md:flex-row justify-between items-end mb-10" data-aos="fade-up">
          <div class="max-w-xl">
            <h2 class="text-primary font-bold tracking-wider uppercase text-sm mb-2">Gita Car Rental</h2>
            <h3 class="text-3xl md:text-4xl font-extrabold text-[#2D2D2D] mb-4">Koleksi Motor</h3>
            <p class="text-[#6C757D]">Bergesekan dengan angin sore Canggu! Pilih motor yang lincah dan gesit untuk
              selap-selip anti macet atau menelusuri hidden beach.</p>
          </div>
          <a href="{{ route('katalog.motor') }}"
            class="mt-4 md:mt-0 inline-flex items-center text-primary font-semibold hover:text-[#248f7f] border-b-2 border-transparent hover:border-[#248f7f] transition">
            Lihat Semua Motor <i class="fas fa-arrow-right ml-2"></i>
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          @forelse($motors as $motor)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group"
              data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
              <div class="relative h-48 overflow-hidden">
                @if($motor->foto)
                  <img src="{{ $motor->foto_url }}" alt="{{ $motor->merk }}"
                    onerror="this.onerror=null; this.src='{{ $motor->placeholder_foto_url }}';"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                  <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                    <i class="fas fa-motorcycle text-4xl"></i>
                  </div>
                @endif
              </div>
              <div class="p-5">
                <h4 class="text-lg font-bold text-[#2D2D2D] mb-1">{{ $motor->nama_kendaraan }}</h4>
                <div class="text-xs text-[#6C757D] mb-4 flex gap-3">
                  <span>{{ ucfirst($motor->jenis_kendaraan) }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                  <div>
                    <span class="text-lg font-bold text-[#2FAE9B]">Rp
                      {{ number_format($motor->harga_sewa_per_hari, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-500">/hr</span>
                  </div>
                  <a href="https://wa.me/628123929934?text=Halo%20Gita%20Car%20Rental,%20saya%20tertarik%20menyewa%20Motor%20{{ urlencode($motor->nama_kendaraan) }}"
                    target="_blank"
                    class="bg-[#F5F6F7] hover:bg-[#2FAE9B] hover:text-white text-[#2D2D2D] px-4 py-2 rounded-lg font-semibold text-xs transition-colors flex items-center gap-1.5">
                    <i class="fab fa-whatsapp"></i> Sewa
                  </a>
                </div>
              </div>
            </div>
          @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-gray-300">
              <i class="fas fa-motorcycle text-4xl text-gray-300 mb-4 block"></i>
              <p class="text-gray-500">Belum ada motor yang tersedia saat ini.</p>
            </div>
          @endforelse
        </div>
      </div>

    </div>
  </section>

  <!-- ===== CARA & SYARAT ===== -->
  <section id="syarat" class="py-20 bg-white border-y border-gray-100 scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid lg:grid-cols-2 gap-16 items-center">
        <!-- Image / Illustration -->
        <div data-aos="fade-right">
          <div class="relative">
            <div
              class="absolute inset-0 bg-gradient-to-tr from-[#2FAE9B] to-[#6ED3C2] rounded-3xl transform rotate-3 scale-105 opacity-20">
            </div>
            <img src="{{ asset('img/galeri_landing/IMG-20250925-WA0024.jpg') }}" alt="Proses Serah Terima"
              onerror="this.src='https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=2070&auto=format&fit=crop'"
              class="w-full h-auto rounded-3xl shadow-xl relative z-10 object-cover object-center h-[500px]">
          </div>
        </div>

        <!-- Text Content -->
        <div data-aos="fade-left">
          <h2 class="text-primary font-bold tracking-wider uppercase text-sm mb-2">Simpel & Aman</h2>
          <h3 class="text-3xl md:text-4xl font-extrabold text-[#2D2D2D] mb-6">Cara Rental Gak Pakai Ribet!</h3>
          <p class="text-[#6C757D] text-lg mb-8 leading-relaxed">Nikmati transparansi dan kejelasan dari awal hingga
            akhir. Kami memastikan proses penyewaan Anda membangun rasa aman.</p>

          <div class="space-y-6">
            <!-- Step 1 -->
            <div class="flex">
              <div class="flex-shrink-0 mr-4">
                <div
                  class="w-10 h-10 rounded-full bg-[#EAF8F6] text-[#2FAE9B] flex items-center justify-center font-bold text-lg">
                  1</div>
              </div>
              <div>
                <h4 class="text-lg font-bold text-[#2D2D2D]">Pilih & Pesan Kendaraan</h4>
                <p class="text-[#6C757D] text-sm mt-1">Cari mobil/motor di website, tentukan tanggal, dan lakukan
                  pendaftaran akun/login.</p>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="flex">
              <div class="flex-shrink-0 mr-4">
                <div
                  class="w-10 h-10 rounded-full bg-[#EAF8F6] text-[#2FAE9B] flex items-center justify-center font-bold text-lg">
                  2</div>
              </div>
              <div>
                <h4 class="text-lg font-bold text-[#2D2D2D]">Upload & Bayar</h4>
                <p class="text-[#6C757D] text-sm mt-1">Isi detail lengkap, kirim permohonan, lalu tunggu konfirmasi
                  harga untuk upload bukti pembayaran.</p>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="flex">
              <div class="flex-shrink-0 mr-4">
                <div
                  class="w-10 h-10 rounded-full bg-primary-gradient text-white flex items-center justify-center font-bold text-lg shadow-md cursor-help group relative">
                  3
                  <span
                    class="absolute -top-10 scale-0 transition-all rounded bg-gray-800 p-2 text-xs text-white group-hover:scale-100 whitespace-nowrap">Ini
                    Syarat Wajib Lho!</span>
                </div>
              </div>
              <div>
                <h4 class="text-lg font-bold text-[#2D2D2D]">Serah Terima & Identitas</h4>
                <p class="text-[#6C757D] text-sm mt-1">Saat mengambil kendaraan, serahkan <span
                    class="font-semibold text-primary">KTP Asli / Passport</span>. Kami juga akan melakukan foto serah
                  terima bersama unit.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== LOCATION ===== -->
  <section id="contact" class="py-20 bg-[#F5F6F7] scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-3xl overflow-hidden shadow-lg">
        <div class="grid lg:grid-cols-2">
          <!-- Text -->
          <div class="p-10 lg:p-14 flex flex-col justify-center" data-aos="fade-right">
            <h2 class="text-3xl font-extrabold text-[#2D2D2D] mb-4">Mampir Ke Garasi Kami</h2>
            <p class="text-[#6C757D] text-lg mb-8 leading-relaxed">
              Tim kami standby untuk menyiapkan kendaraan Anda. Jika ada pertanyaan, jangan ragu untuk datang langsung.
            </p>

            <div class="mb-8">
              <div class="flex items-start mb-4">
                <i class="fas fa-map-marker-alt text-primary mt-1 mr-4 text-xl"></i>
                <div>
                  <h5 class="font-bold text-[#2D2D2D] mb-1">Alamat</h5>
                  <p class="text-[#6C757D]">Jl. Tanah Barak No.47a, Canggu,<br>Kec. Kuta Utara, Kab. Badung,<br>Bali
                    80351</p>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap gap-4 mt-auto">
              <a href="https://maps.google.com/?q=Jl.+Tanah+Barak+No.47a+Canggu+Bali" target="_blank"
                class="bg-[#F5F6F7] text-[#2D2D2D] hover:bg-gray-200 px-6 py-3 rounded-xl font-bold transition flex items-center">
                <i class="fas fa-directions mr-2 text-primary"></i> Arahkan Saya
              </a>
              <a href="https://wa.me/628123929934" target="_blank"
                class="bg-[#25D366] hover:bg-[#128C7E] text-white px-6 py-3 rounded-xl font-bold transition flex items-center shadow-lg shadow-[#25D366]/30">
                <i class="fab fa-whatsapp text-xl mr-2"></i> Tanya via WA
              </a>
            </div>
          </div>

          <!-- Map iframe -->
          <div class="h-64 lg:h-auto min-h-[400px] w-full" data-aos="fade-left">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.40938686617!2d115.13962657593672!3d-8.652516788279888!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd24756bf0a68d7%3A0xc6c4214ef5e927c8!2sJl.%20Tanah%20Barak%20No.47a%2C%20Canggu%2C%20Kec.%20Kuta%20Utara%2C%20Kabupaten%20Badung%2C%20Bali%2080351!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid"
              width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade" class="w-full h-full object-cover">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CLOSING CTA ===== -->
  <section class="py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-primary-gradient"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center" data-aos="zoom-in">
      <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Siap Menjelajah Bali Tanpa Ribet?</h2>
      <p class="text-[#EAF8F6] text-xl mb-10 max-w-2xl mx-auto opacity-90">Sewa cepat, unit dijamin top condition, harga
        transparan. Jangan biarkan rencana jalan-jalanmu mandek!</p>

      <div class="flex flex-col sm:flex-row justify-center items-center gap-5">
        <a href="#katalog"
          class="w-full sm:w-auto bg-white text-[#2FAE9B] hover:bg-gray-50 px-8 py-4 rounded-full font-bold text-lg transition shadow-xl hover:-translate-y-1">
          Sewa Sekarang
        </a>
        <a href="https://wa.me/628123929934" target="_blank"
          class="w-full sm:w-auto text-white border-2 border-white hover:bg-white/10 px-8 py-4 rounded-full font-bold text-lg transition flex items-center justify-center group">
          <i class="fab fa-whatsapp mr-2 group-hover:scale-110 transition-transform"></i> Tanya Ketersediaan
        </a>
      </div>
    </div>
  </section>

  <!-- Floating WhatsApp Button (Fixed on all scroll) -->
  <a href="https://wa.me/628123929934" target="_blank" rel="noopener noreferrer"
    class="fixed bottom-6 right-6 z-50 w-16 h-16 bg-[#25D366] hover:bg-[#128C7E] rounded-full flex items-center justify-center shadow-lg hover:shadow-2xl hover:scale-110 transition-all duration-300 group ring-4 ring-white">
    <i class="fab fa-whatsapp text-white text-3xl"></i>
    <span
      class="absolute right-full mr-4 bg-white text-[#2D2D2D] font-semibold text-sm px-4 py-2 rounded-xl shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none origin-right transform group-hover:-translate-x-2">
      Chat dengan Admin Gita Car Rental! 🚙
      <div class="absolute right-[-6px] top-1/2 -translate-y-1/2 w-3 h-3 bg-white transform rotate-45"></div>
    </span>
  </a>

</div>
