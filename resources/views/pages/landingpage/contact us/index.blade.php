<?php

use function Livewire\Volt\{layout, title};

layout('components.layouts.landing');
title('Contact Us - Gita Car Rental');

?>

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  .contact-hero-bg {
    background-image: url('{{ asset("img/hero-canggu.jpg") }}');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
  }

  .glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .icon-box {
    background: linear-gradient(135deg, #2FAE9B 0%, #6ED3C2 100%);
    box-shadow: 0 10px 20px -5px rgba(46, 174, 155, 0.4);
  }

  .hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
  }
</style>

<div class="text-slate-800 bg-[#FAFAFA] min-h-screen pt-[var(--nav-h)] overflow-hidden">

  <!-- Hero Section -->
  <section class="relative pt-20 pb-32 lg:pt-32 lg:pb-40 contact-hero-bg">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/60 to-slate-900/90"></div>
    <div class="container mx-auto px-4 relative z-10 text-center" data-aos="fade-up" data-aos-duration="1000">
      <span
        class="inline-block py-1.5 px-4 rounded-full bg-white/10 text-white text-sm font-semibold tracking-wider uppercase mb-6 border border-white/20 backdrop-blur-md">
        Get In Touch
      </span>
      <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
        Hubungi Kami
      </h1>
      <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto font-light leading-relaxed mb-8">
        Punya pertanyaan tentang layanan sewa mobil & motor kami di Canggu? Tim kami selalu siap membantu Anda
        merencanakan perjalanan yang tak terlupakan.
      </p>
    </div>

    <!-- Decoration Curve -->
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-[#FAFAFA]"
      style="clip-path: polygon(0 100%, 100% 100%, 100% 0, 50% 100%, 0 0);"></div>
  </section>

  <!-- Main Content Grid -->
  <section class="container mx-auto px-4 -mt-20 relative z-20 pb-24">
    <div class="grid lg:grid-cols-12 gap-8 lg:gap-12">

      <!-- Left Column: Contact Cards -->
      <div class="lg:col-span-5 space-y-6">
        <!-- Location Card -->
        <div class="glass-card rounded-3xl p-8 hover-lift" data-aos="fade-up" data-aos-delay="100">
          <div class="flex items-start gap-5">
            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center text-white shrink-0">
              <i class="fas fa-map-marker-alt text-2xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Lokasi Garasi</h3>
              <p class="text-slate-600 leading-relaxed mb-4">Jl. Tanah Barak No.47a, Canggu,<br>Kec. Kuta Utara, Kab.
                Badung,<br>Bali 80351</p>
              <a href="https://maps.google.com/?q=Jl.+Tanah+Barak+No.47a+Canggu+Bali" target="_blank"
                class="text-[#2FAE9B] font-semibold hover:text-[#248f7f] inline-flex items-center group transition-colors">
                Lihat di Google Maps <i
                  class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Phone/WhatsApp Card -->
        <div class="glass-card rounded-3xl p-8 hover-lift" data-aos="fade-up" data-aos-delay="200">
          <div class="flex items-start gap-5">
            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center text-white shrink-0">
              <i class="fab fa-whatsapp text-3xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">WhatsApp Kami</h3>
              <p class="text-slate-600 leading-relaxed mb-4">Fast response! Tim reservasi kami siap menjawab pertanyaan
                Anda.</p>
              <a href="https://wa.me/628123929934" target="_blank"
                class="text-slate-900 text-lg font-bold hover:text-[#2FAE9B] transition-colors">
                +62 812-3929-934
              </a>
            </div>
          </div>
        </div>

        <!-- Email Card -->
        <div class="glass-card rounded-3xl p-8 hover-lift" data-aos="fade-up" data-aos-delay="300">
          <div class="flex items-start gap-5">
            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center text-white shrink-0">
              <i class="fas fa-envelope text-xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">Email</h3>
              <p class="text-slate-600 leading-relaxed mb-4">Untuk kerjasama korporat atau grup, kirimkan detail Anda
                melalui email.</p>
              <a href="mailto:info@gitacarrental.com"
                class="text-slate-900 text-lg font-bold hover:text-[#2FAE9B] transition-colors break-all">
                info@gitacarrental.com
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Contact Form & Map -->
      <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="400">
        <div class="glass-card rounded-3xl p-8 md:p-10 h-full flex flex-col">
          <div class="mb-8">
            <h2 class="text-3xl font-bold text-slate-900 mb-3">Kirim Pesan Langsung</h2>
            <p class="text-slate-600">Isi form di bawah ini dan kami akan membalasnya secepat mungkin.</p>
          </div>

          <form id="contact-form" onsubmit="sendToWhatsApp(event)" class="space-y-6 flex-grow flex flex-col">
            <div class="grid md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Nama Lengkap</label>
                <input type="text" id="wa-nama" placeholder="John Doe" required
                  class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2FAE9B] focus:border-transparent transition-all">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Nomor WhatsApp</label>
                <input type="text" id="wa-nomor" placeholder="+62 812..." required
                  class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2FAE9B] focus:border-transparent transition-all">
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Subjek</label>
              <select id="wa-subjek"
                class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2FAE9B] focus:border-transparent transition-all appearance-none cursor-pointer">
                <option value="Pertanyaan Umum">Pertanyaan Umum</option>
                <option value="Informasi Harga & Promo">Informasi Harga & Promo</option>
                <option value="Kerjasama / Vendor">Kerjasama / Vendor</option>
                <option value="Komplain & Masukan">Komplain & Masukan</option>
              </select>
            </div>

            <div class="space-y-2 flex-grow">
              <label class="text-sm font-semibold text-slate-700">Pesan Anda</label>
              <textarea id="wa-pesan" placeholder="Tuliskan pesan Anda dengan detail..." rows="5" required
                class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#2FAE9B] focus:border-transparent transition-all resize-none h-32"></textarea>
            </div>

            <button type="submit"
              class="w-full bg-gradient-to-r from-[#2FAE9B] to-[#6ED3C2] hover:from-[#248f7f] hover:to-[#5bc0b0] text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 mt-4">
              Kirim Pesan
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Map Full Width Section -->
  <section class="w-full h-[500px] grayscale hover:grayscale-0 transition-all duration-1000 ease-in-out"
    data-aos="fade-in">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.40938686617!2d115.13962657593672!3d-8.652516788279888!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd24756bf0a68d7%3A0xc6c4214ef5e927c8!2sJl.%20Tanah%20Barak%20No.47a%2C%20Canggu%2C%20Kec.%20Kuta%20Utara%2C%20Kabupaten%20Badung%2C%20Bali%2080351!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid"
      width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
      referrerpolicy="no-referrer-when-downgrade" class="w-full h-full object-cover">
    </iframe>
  </section>

  <!-- Floating WhatsApp Button -->
  <a href="https://wa.me/628123929934" target="_blank" rel="noopener noreferrer"
    class="fixed bottom-24 md:bottom-6 right-6 z-50 w-16 h-16 bg-[#25D366] hover:bg-[#128C7E] rounded-full flex items-center justify-center shadow-lg hover:shadow-2xl hover:scale-110 transition-all duration-300 group ring-4 ring-white">
    <i class="fab fa-whatsapp text-white text-3xl"></i>
    <span
      class="absolute right-full mr-4 bg-white text-[#2D2D2D] font-semibold text-sm px-4 py-2 rounded-xl shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none origin-right transform group-hover:-translate-x-2">
      Chat dengan Admin Gita Car Rental! 🚙
      <div class="absolute right-[-6px] top-1/2 -translate-y-1/2 w-3 h-3 bg-white transform rotate-45"></div>
    </span>
  </a>
</div>

<script>
  function sendToWhatsApp(e) {
    e.preventDefault();
    
    // Ambil nilai dari input
    const nama = document.getElementById('wa-nama').value;
    const nomor = document.getElementById('wa-nomor').value;
    const subjek = document.getElementById('wa-subjek').value;
    const pesan = document.getElementById('wa-pesan').value;
    
    if(!nama || !nomor || !pesan) {
        return;
    }

    // Format pesan
    const text = `Halo Gita Car Rental,\n\nSaya menghubungi Anda terkait *${subjek}*.\n\n*Nama Lengkap:* ${nama}\n*Nomor WA:* ${nomor}\n\n*Pesan:*\n${pesan}`;
    
    // Encode URL
    const encodedText = encodeURIComponent(text);
    
    // Nomor WA tujuan
    const waNumber = '628123929934';
    
    // Buat URL WhatsApp
    const waUrl = `https://wa.me/${waNumber}?text=${encodedText}`;
    
    // Buka di tab baru
    window.open(waUrl, '_blank');
  }

  document.addEventListener('livewire:navigated', () => {
    if (typeof AOS !== 'undefined') {
      setTimeout(() => AOS.refresh(), 100);
    }
  });
</script>