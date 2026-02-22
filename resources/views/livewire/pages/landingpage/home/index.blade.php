<?php

use function Livewire\Volt\{layout, title, state, mount};
use App\Models\Kamar;

layout('components.layouts.landing');
title('Gita Car Rental');

state([
  'kamars' => []
]);

mount(function () {
  $this->kamars = Kamar::where('status', 'aktif')->get();
});

?>
<style>
  :root {
    --nav-h: 84px;
  }

  .reveal {
    opacity: 0;
    transform: translateY(24px);
    will-change: transform, opacity;
  }


  /* Debug info styling */
  .debug-info {
    font-family: monospace;
    font-size: 10px;
    max-width: 200px;
    word-break: break-all;
  }

  /* fallback aksesibilitas bila kontras tinggi */
  @media (prefers-contrast: more) {
    .hero-head {
      color: #eaf2ff !important;
      -webkit-text-fill-color: currentColor;
      background: none !important;
      /* disable gradient text */
      text-shadow: 0 1px 2px rgba(0, 0, 0, .35);
    }
  }
</style>

<div class="text-slate-800">
  <!-- Alert Messages -->
  <x-alert />

  <!-- ===== HERO ===== -->
  <section id="hero"
    class="relative min-h-[calc(80svh-var(--nav-h))] md:min-h-[calc(80dvh-var(--nav-h))] pt-[calc(var(--nav-h)+4rem)] pb-8 md:pb-12 overflow-hidden">

    <!-- BG Cover Image (LCP) -->
    <img src="{{ asset('img/homesection.jpg') }}" alt="Gita Car Rental" fetchpriority="high"
      class="absolute inset-0 w-full h-full object-cover object-center">

    <!-- Overlay gelap -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 via-slate-900/50 to-slate-900/30"></div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-6 md:px-8 flex items-center justify-start h-full">
      <div class="max-w-4xl text-white py-12 md:py-20 lg:py-32">

        <!-- Headings -->
        <div class="mb-8 md:mb-10">
          <h1 id="hero-title-1"
            class="hero-head text-[clamp(2.5rem,6vw,5rem)] md:text-6xl lg:text-7xl font-bold leading-tight text-white opacity-0"
            style="font-family: 'Playfair Display', 'Cormorant Garamond', serif; font-weight: 500; letter-spacing: 0.02em; text-shadow: 3px 3px 10px rgba(0,0,0,0.9), -2px -2px 4px rgba(0,0,0,0.7), 0 0 20px rgba(0,0,0,0.5);">
            <span class="block">Stay Quietly</span>
            <span class="block">Close to Nature</span>
          </h1>
        </div>

        <!-- Description -->
        <p class="mb-10 md:mb-12 text-lg md:text-xl lg:text-2xl text-white/95 leading-relaxed max-w-2xl"
          data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
          Escape to Gita Car Rental a peaceful riverside retreat in Pererenan, Bali, where comfort
          meets serenity.
        </p>

        <!-- CTA -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4" data-aos="fade-up"
          data-aos-duration="800" data-aos-delay="500">

          <a href="{{ route('landingpage.reservasi') }}" class="inline-flex items-center justify-center gap-1 rounded-xl bg-[#ede6d2] hover:bg-[#e0d7c0]
            px-6 py-3 text-slate-800 font-semibold text-base shadow-lg
            transition-all duration-300 transform hover:scale-105
            focus:outline-none focus:ring-4 focus:ring-[#ede6d2]/50">
            <span>Book Your Stay</span>
          </a>

          <a href="{{ route('landingpage.contact') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/20 hover:bg-white/30
            backdrop-blur-sm px-6 py-3 text-white font-semibold text-base
            border border-white/30 shadow-md
            transition-all duration-300 transform hover:scale-105
            focus:outline-none focus:ring-4 focus:ring-white/40">
            <span>Contact Us</span>
          </a>

        </div>


      </div>

      <!-- Decorative blobs (target GSAP) -->
      <div id="blob-amber"
        class="absolute -right-32 top-32 w-96 h-96 rounded-full bg-gradient-to-br from-amber-300/20 to-transparent blur-3xl">
      </div>
      <div class="absolute -left-24 bottom-32 w-80 h-80 rounded-full bg-blue-500/20 blur-3xl"></div>
  </section>

  <!-- ===== FASILITAS ===== -->
  <section id="fasilitas" class="scroll-mt-[var(--nav-h)] bg-white">
    <div class="container mx-auto px-4 py-16">
      <div class="text-center">
        <h2 class="text-sm md:text-base font-sans uppercase tracking-wide text-slate-900 mb-4" data-aos="fade-up"
          data-aos-duration="800">
          FACILITIES OVERVIEW
        </h2>
        <h3 class="text-1xl md:text-2xl lg:text-3xl font-serif font-semibold text-slate-900 mb-6" data-aos="fade-up"
          data-aos-duration="800" data-aos-delay="100">
          Gita Car Rental
        </h3>
        <p class="text-sm md:text-base text-slate-600 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
          data-aos-duration="800" data-aos-delay="200">
          Our facilities are designed to enhance your stay, providing a seamless blend of comfort and nature's embrace
        </p>
      </div>
      <div class="mt-12 flex justify-center">
        <div class="w-full max-w-[800px] flex justify-center items-center gap-24">
          <!-- Kitchen -->
          <div class="facility-card flex flex-col items-center" data-aos="fade-up" data-aos-duration="800"
            data-aos-delay="300">
            <div class="facility-icon mb-3 text-[#A3B18A]">
              <i class="fas fa-utensils text-2xl md:text-3xl"></i>
            </div>
            <h3 class="text-base md:text-lg font-medium text-[#A3B18A]">Kitchen</h3>
          </div>

          <!-- Pool -->
          <div class="facility-card flex flex-col items-center" data-aos="fade-up" data-aos-duration="800"
            data-aos-delay="400">
            <div class="facility-icon mb-3 text-[#A3B18A]">
              <i class="fas fa-swimming-pool text-2xl md:text-3xl"></i>
            </div>
            <h3 class="text-base md:text-lg font-medium text-[#A3B18A]">Pool</h3>
          </div>

          <!-- Wifi -->
          <div class="facility-card flex flex-col items-center" data-aos="fade-up" data-aos-duration="800"
            data-aos-delay="500">
            <div class="facility-icon mb-3 text-[#A3B18A]">
              <i class="fas fa-wifi text-2xl md:text-3xl"></i>
            </div>
            <h3 class="text-base md:text-lg font-medium text-[#A3B18A]">Wifi</h3>
          </div>

          <!-- Parking -->
          <div class="facility-card flex flex-col items-center" data-aos="fade-up" data-aos-duration="800"
            data-aos-delay="600">
            <div class="facility-icon mb-3 text-[#A3B18A]">
              <i class="fas fa-parking text-2xl md:text-3xl"></i>
            </div>
            <h3 class="text-base md:text-lg font-medium text-[#A3B18A]">Parking</h3>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== ABOUT ===== -->
  <section id="about" class="scroll-mt-[var(--nav-h)] bg-[#F3EEE1] py-20">
    <div class="container mx-auto px-4">
      <!-- Header Section -->
      <div class="text-center mb-16">
        <p class="text-sm md:text-base uppercase tracking-wider text-slate-600 mb-4" data-aos="fade-up"
          data-aos-duration="800">
          DISCOVER the COLLECTION
        </p>
        <h2 class="text-1xl md:text-2xl lg:text-3xl font-serif font-semibold text-slate-900 mb-6" data-aos="fade-up"
          data-aos-duration="800" data-aos-delay="100">
          A Riverside Room Designed for Your Peaceful Stay
        </h2>
        <p class="text-sm md:text-base text-slate-600 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
          data-aos-duration="800" data-aos-delay="200">
          Imagine waking up to the gentle sound of the river and soft morning light. This serene minimalist room offers
          the perfect retreat for rest, reflection, and reconnection with nature.
        </p>
      </div>

      <!-- Main Content Block (Two Columns) -->
      <div class="grid lg:grid-cols-2 gap-12 items-center mb-12">
        <!-- Left Column: Image -->
        <div class="order-1 lg:order-1" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
          @php
            $firstKamar = $kamars->first();
            $mainImage = asset('img/galeri_landing/IMG-20250925-WA0028.jpg');
          @endphp
          <img src="{{ $mainImage }}"
            class="w-full rounded-2xl shadow-xl object-cover h-[300px] md:h-[320px] lg:h-[340px]">

        </div>

        <!-- Right Column: Text Content -->
        <div class="order-2 lg:order-2" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
          <h3 class="text-1xl md:text-2xl font-bold text-slate-900 mb-6">
            Gita Car Rental
          </h3>
          <p class="text-sm md:text-base text-slate-700 leading-relaxed mb-4">
            Live the riverside escape you’ve been longing for at Gita Car Rental
            a serene hideaway surrounded by lush greenery and the gentle sound of flowing water.
            Designed with a modern minimalist touch, it’s the perfect retreat to unwind and reconnect with nature in
            Bali’s tranquil side.
            Beyond its comfortable bedding and contemporary layout, the studio is thoughtfully curated for those with a
            busy itinerary in Bali.
            It is more than just a place to sleep; it is a refined home base that provides everything you need for a
            productive and rejuvenating stay.
          </p>

          <a href="{{ route('landingpage.kamar-fasilitas') }}"
            class="inline-flex items-center text-slate-900 font-semibold hover:text-slate-700 transition-colors duration-300 border-b-2 border-slate-900 hover:border-slate-700">
            See Room Detail
            <i class="fas fa-arrow-right ml-2"></i>
          </a>
        </div>
      </div>

      <!-- Gallery Thumbnails Section -->
      @php
        $roomThumbnails = [
          [
            'title' => 'Gita Car Rental',
            'image' => asset('img/galeri_landing/IMG-20250925-WA0023.jpg'),
          ],
          [
            'title' => 'Gita Car Rental',
            'image' => asset('img/galeri_landing/IMG-20250925-WA0024.jpg'),
          ],
          [
            'title' => 'Gita Car Rental',
            'image' => asset('img/galeri_landing/IMG-20250925-WA0025.jpg'),
          ],
          [
            'title' => 'Gita Car Rental',
            'image' => asset('img/galeri_landing/IMG-20250925-WA0026.jpg'),
          ],
        ];
      @endphp
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($roomThumbnails as $index => $room)
          <div class="relative group overflow-hidden rounded-lg" data-aos="fade-up" data-aos-duration="800"
            data-aos-delay="{{ 500 + ($index * 100) }}">
            <img src="{{ $room['image'] }}" alt="{{ $room['title'] }}"
              class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
              <h4 class="font-bold text-md mb-1">{{ $room['title'] }}</h4>
              <p class="text-sm text-white/90">Pererenan, Bali</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ===== FAQ ===== -->
  <section id="faq" class="scroll-mt-[var(--nav-h)] bg-white py-12 md:py-16">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto">
        <!-- Heading Section -->
        <div class="text-center mb-12 reveal" data-aos="fade-up" data-aos-duration="800">
          <h2 class="text-1xl md:text-2xl lg:text-3xl font-serif font-bold text-slate-900 mb-4"
            style="font-family: 'Playfair Display', 'Cormorant Garamond', serif;">
            Frequently Asked Questions
          </h2>
          <p class="text-sm md:text-base text-slate-600 max-w-2xl mx-auto">
            Everything you need to know about your stay at Gita Car Rental.
          </p>
        </div>

        <!-- FAQ Accordion -->
        <div class="reveal" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
          <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="faq-item">
              <button
                class="faq-button w-full flex items-center justify-between py-5 text-left transition-colors duration-200 border-b border-slate-200"
                data-faq="1">
                <span class="font-semibold text-slate-900 pr-4">Are Experience and Dine at Gita Car Rental separate
                  charges?</span>
                <div
                  class="faq-icon-wrapper flex-shrink-0 w-7 h-7 rounded-full bg-[#1D2D20] flex items-center justify-center transition-transform duration-200">
                  <svg class="faq-icon w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    data-icon-type="minus">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                  </svg>
                  <svg class="faq-icon-plus w-4 h-4 text-white hidden" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" data-icon-type="plus">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
              </button>
              <div class="faq-content pb-5 pt-2" data-content="1">
                <p class="text-slate-600 leading-relaxed">
                  Yes, Experience and Dine services at Gita Car Rental are separate charges. These additional services,
                  such as yoga sessions, spa treatments, and dining experiences, are not included in the room rate and
                  will be charged separately based on your selection.
                </p>
              </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item">
              <button
                class="faq-button w-full flex items-center justify-between py-5 text-left transition-colors duration-200 border-b border-slate-200"
                data-faq="2">
                <span class="font-semibold text-slate-900 pr-4">What are the check-in and check-out times?</span>
                <div
                  class="faq-icon-wrapper flex-shrink-0 w-7 h-7 rounded-full bg-[#1D2D20] flex items-center justify-center transition-transform duration-200">
                  <svg class="faq-icon w-4 h-4 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    data-icon-type="minus">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                  </svg>
                  <svg class="faq-icon-plus w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    data-icon-type="plus">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
              </button>
              <div class="faq-content hidden pb-5 pt-2" data-content="2">
                <p class="text-slate-600 leading-relaxed">
                  Check-in time is at 2:00 PM and check-out time is at 12:00 NN. Early check-in or late check-out may be
                  available upon request, subject to availability and may incur additional charges.
                </p>
              </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item">
              <button
                class="faq-button w-full flex items-center justify-between py-5 text-left transition-colors duration-200 border-b border-slate-200"
                data-faq="3">
                <span class="font-semibold text-slate-900 pr-4">Is parking available at the guest house?</span>
                <div
                  class="faq-icon-wrapper flex-shrink-0 w-7 h-7 rounded-full bg-[#1D2D20] flex items-center justify-center transition-transform duration-200">
                  <svg class="faq-icon w-4 h-4 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    data-icon-type="minus">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                  </svg>
                  <svg class="faq-icon-plus w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    data-icon-type="plus">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
              </button>
              <div class="faq-content hidden pb-5 pt-2" data-content="3">
                <p class="text-slate-600 leading-relaxed">
                  Yes, parking is available at Gita Car Rental. We provide complimentary parking for our guests. Please
                  inform us in advance if you will be bringing a vehicle.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONI ===== -->
  <section id="testimoni" class="scroll-mt-[var(--nav-h)] bg-[#E0E5D8] py-20">
    <div class="container mx-auto px-4">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h2 class="text-1xl md:text-2xl lg:text-3xl font-serif font-bold text-slate-900 mb-4" data-aos="fade-up"
          data-aos-duration="800" style="font-family: 'Playfair Display', 'Cormorant Garamond', serif;">
          What Our Guests Say
        </h2>
        <p class="text-sm md:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up"
          data-aos-duration="800" data-aos-delay="100">
          Hear from our guests about their experiences at Gita Car Rental.
        </p>
      </div>

      @php
        $testimonials = \App\Models\Review::where('tampil_home', true)
          ->orderByDesc('created_at')
          ->limit(8)
          ->get();
      @endphp

      @if ($testimonials->isEmpty())
        <div class="text-center text-slate-600">
          No reviews have been posted yet.
        </div>
      @else
        <!-- Testimonial Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
          @foreach($testimonials as $index => $testimonial)
            @php
              $initials = collect(explode(' ', trim($testimonial->reviewer_name)))
                ->filter()
                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                ->take(2)
                ->implode('');
            @endphp
            <div class="reveal bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300"
              data-aos="fade-up" data-aos-duration="800" data-aos-delay="{{ 200 + ($index * 100) }}">
              <!-- Profile Section -->
              <div class="flex items-center gap-3 mb-4">
                <div
                  class="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center text-sm font-semibold border-2 border-slate-100">
                  {{ $initials ?: 'GU' }}
                </div>
                <div>
                  <h4 class="font-bold text-slate-900 text-base">{{ $testimonial->reviewer_name }}</h4>
                  @if($testimonial->reviewer_location)
                    <p class="text-sm text-slate-500">{{ $testimonial->reviewer_location }}</p>
                  @endif
                </div>
              </div>

              <!-- Rating Stars -->
              <div class="mb-4 flex gap-1">
                @for($i = 0; $i < $testimonial->rating; $i++)
                  <i class="fas fa-star text-black text-sm"></i>
                @endfor
              </div>

              <!-- Testimonial Text -->
              <blockquote class="text-slate-700 text-sm leading-relaxed">
                "{{ $testimonial->ulasan }}"
              </blockquote>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </section>



  <!-- ===== LOCATION / CONTACT ===== -->
  <section id="location" class="scroll-mt-[var(--nav-h)] bg-white py-20">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-3 gap-8 lg:gap-12 items-start">
        <!-- Left Column: Map (2/3 width) -->
        <div class="lg:col-span-2 reveal" data-aos="fade-right" data-aos-duration="800">
          <div class="w-full rounded-2xl overflow-hidden shadow-xl">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63112.41892927758!2d115.08940430033601!3d-8.641401328206275!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd239a2f4166f51%3A0xb816ca5375b78c65!2sWatugangga%20Riverside!5e0!3m2!1sid!2sid!4v1765610940344!5m2!1sid!2sid"
              width="100%" height="496" style="border:0;" allowfullscreen="" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade" class="w-full">
            </iframe>
          </div>
        </div>

        <!-- Right Column: Contact Info (1/3 width) -->
        <div class="lg:col-span-1 reveal" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
          <div class="bg-[#F3EEE1] rounded-2xl p-8 shadow-lg">
            <!-- Heading -->
            <p class="text-sm md:text-base font-sans uppercase tracking-wide text-slate-600 mb-4" data-aos="fade-up"
              data-aos-duration="800">
              GETTING THERE
            </p>

            <!-- Title -->
            <h2 class="text-1xl md:text-2xl font-bold text-slate-900 mb-6"
              style="font-family: 'Playfair Display', 'Cormorant Garamond', serif; font-weight: 700;">
              Gita Car Rental Location
            </h2>

            <!-- Address -->
            <div class="mb-6" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
              <div class="flex items-start gap-3">
                <i class="fas fa-map-marker-alt text-slate-500 text-base mt-1"></i>
                <div>
                  <p class="text-slate-700 leading-relaxed">
                    Gg. Salak No.Br, Pererenan, Kec. Mengwi, Kabupaten Badung, Bali 80351
                  </p>
                </div>
              </div>
            </div>

            <!-- Contact Methods -->
            <div class="space-y-4 mb-6">
              <!-- Email -->
              <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">BY EMAIL</p>
                <a href="mailto:watuganggariverside@gmail.com"
                  class="flex items-center gap-3 text-slate-900 hover:text-slate-700 transition-colors duration-300">
                  <i class="fas fa-envelope text-slate-500"></i>
                  <span class="text-base">watuganggariverside@gmail.com</span>
                </a>
              </div>

              <!-- WhatsApp -->
              <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">BY WHATSAPP</p>
                <a href="https://wa.me/6287862173133" target="_blank" rel="noopener noreferrer"
                  class="flex items-center gap-3 text-slate-900 hover:text-slate-700 transition-colors duration-300">
                  <i class="fab fa-whatsapp text-slate-500"></i>
                  <span class="text-base">087862173133</span>
                </a>
              </div>
            </div>

            <!-- Check-in/Check-out -->
            <div class="pt-6 border-t border-slate-300" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
              <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-clock text-slate-500"></i>
                <div class="text-slate-700">
                  <p class="font-semibold">Check-in: <span class="font-normal">2:00 PM</span></p>
                  <p class="font-semibold">Check-out: <span class="font-normal">12:00 NN</span></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Floating WhatsApp Button -->
  <a href="https://wa.me/6287862173133" target="_blank" rel="noopener noreferrer"
    class="fixed bottom-[50px] right-[75px] z-50 w-14 h-14 bg-[#25D366] hover:bg-[#20BA5A] rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 group"
    aria-label="Contact us on WhatsApp">
    <i class="fab fa-whatsapp text-white text-2xl"></i>
    <span
      class="absolute -top-12 right-0 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none">
      Chat with us
    </span>
  </a>
</div>

<script>
  (function () {
    const ready = (cb) => (document.readyState === 'loading') ?
      document.addEventListener('DOMContentLoaded', cb) :
      cb();

    // sinkronkan --nav-h dengan tinggi navbar aktual
    const syncNavH = () => {
      const n = document.getElementById('main-navbar');
      if (!n) return;
      document.documentElement.style.setProperty('--nav-h', n.offsetHeight + 'px');
    };
    addEventListener('load', syncNavH);
    addEventListener('resize', syncNavH);


    ready(() => {
      // Inisialisasi AOS
      if (window.AOS) {
        AOS.init({
          duration: 800,
          easing: 'ease-in-out',
          once: true,
          offset: 100
        });
      } else {
        console.warn('AOS belum ter-load');
      }

      if (!window.gsap) {
        console.warn('GSAP belum ter-load');
        return;
      }
      if (window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);
      if (window.ScrollToPlugin) gsap.registerPlugin(ScrollToPlugin);

      // HERO entrance
      gsap.to('#hero .reveal', {
        opacity: 1,
        y: 0,
        duration: 0.9,
        ease: 'power2.out',
        stagger: 0.08,
        delay: 0.15
      });

      // Animasi khusus untuk heading hero
      const heroTitle1 = document.getElementById('hero-title-1');
      const heroTitle2 = document.getElementById('hero-title-2');

      if (heroTitle1) {
        // Timeline untuk animasi heading
        const heroTimeline = gsap.timeline({
          delay: 0.3
        });

        heroTimeline.to(heroTitle1, {
          opacity: 1,
          duration: 1.2,
          ease: 'power3.out'
        });

        // Jika h2 ada, tambahkan animasi untuk h2
        if (heroTitle2) {
          heroTimeline.to(heroTitle2, {
            opacity: 1,
            duration: 1.2,
            ease: 'power3.out'
          }, '-=0.6'); // Mulai sebelum animasi pertama selesai
        }
      }

      // Parallax halus pada blob (fixed selector)
      const blob = document.querySelector('#blob-amber');
      if (blob && window.ScrollTrigger) {
        gsap.to(blob, {
          y: 80,
          x: -40,
          ease: 'none',
          scrollTrigger: {
            trigger: '#hero',
            start: 'top top',
            end: 'bottom top',
            scrub: true
          }
        });
      }

      // Animasi section fasilitas
      const facilityCards = gsap.utils.toArray('.facility-card');
      if (facilityCards.length > 0 && window.ScrollTrigger) {
        facilityCards.forEach((card, index) => {
          // Animasi entrance
          gsap.fromTo(card, {
            opacity: 0,
            y: 50,
            scale: 0.8,
            rotationY: -15
          }, {
            opacity: 1,
            y: 0,
            scale: 1,
            rotationY: 0,
            duration: 0.8,
            ease: 'power2.out',
            delay: index * 0.1,
            scrollTrigger: {
              trigger: card,
              start: 'top 85%',
              toggleActions: 'play none none reverse'
            }
          });

          // Hover animation
          const iconElement = card.querySelector('.facility-icon i') || card.querySelector('.facility-icon');
          if (iconElement) {
            card.addEventListener('mouseenter', () => {
              gsap.to(iconElement, {
                scale: 1.15,
                y: -5,
                duration: 0.3,
                ease: 'power2.out'
              });
            });

            card.addEventListener('mouseleave', () => {
              gsap.to(iconElement, {
                scale: 1,
                y: 0,
                duration: 0.3,
                ease: 'power2.out'
              });
            });
          }
        });
      }


      // Scroll reveal
      const items = gsap.utils.toArray('.reveal');
      if (window.ScrollTrigger) {
        items.forEach((el) => {
          gsap.to(el, {
            opacity: 1,
            y: 0,
            duration: 0.7,
            ease: 'power2.out',
            scrollTrigger: {
              trigger: el,
              start: 'top 85%',
              toggleActions: 'play none none none'
            }
          });
        });
      } else {
        const io = new IntersectionObserver((entries) => {
          entries.forEach((e) => {
            if (e.isIntersecting) {
              gsap.to(e.target, {
                opacity: 1,
                y: 0,
                duration: 0.7,
                ease: 'power2.out'
              });
              io.unobserve(e.target);
            }
          });
        }, {
          threshold: 0.15
        });
        items.forEach((el) => io.observe(el));
      }

      // Smooth anchor scroll dengan offset navbar
      document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', (e) => {
          const id = a.getAttribute('href');
          const target = document.querySelector(id);
          if (!target) return;
          const navH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-h')) || 80;
          const y = target.getBoundingClientRect().top + window.pageYOffset - navH;

          if (window.ScrollToPlugin) {
            e.preventDefault();
            gsap.to(window, {
              duration: 0.9,
              scrollTo: y,
              ease: 'power2.out'
            });
          } else {
            // fallback native smooth scroll
            e.preventDefault();
            window.scrollTo({
              top: y,
              behavior: 'smooth'
            });
          }
        });
      });
    });

    // Re-init setelah Livewire navigate
    document.addEventListener('livewire:navigated', () => {
      syncNavH();

      // Re-inisialisasi AOS
      if (window.AOS) {
        AOS.refresh();
      }

      if (!window.gsap) return;
      gsap.utils.toArray('.reveal').forEach((el, i) =>
        gsap.fromTo(el, {
          opacity: 0,
          y: 24
        }, {
          opacity: 1,
          y: 0,
          duration: 0.6,
          delay: 0.04 * i,
          ease: 'power2.out'
        })
      );

      // Re-animasi heading hero setelah navigasi
      const heroTitle1 = document.getElementById('hero-title-1');
      const heroTitle2 = document.getElementById('hero-title-2');

      if (heroTitle1) {
        gsap.set(heroTitle1, {
          opacity: 0
        });
        if (heroTitle2) {
          gsap.set(heroTitle2, {
            opacity: 0
          });
        }

        const heroTimeline = gsap.timeline({
          delay: 0.2
        });
        heroTimeline.to(heroTitle1, {
          opacity: 1,
          duration: 1.2,
          ease: 'power3.out'
        });

        // Jika h2 ada, tambahkan animasi untuk h2
        if (heroTitle2) {
          heroTimeline.to(heroTitle2, {
            opacity: 1,
            duration: 1.2,
            ease: 'power3.out'
          }, '-=0.6');
        }
      }

      // Re-animasi section fasilitas setelah navigasi
      const facilityCards = gsap.utils.toArray('.facility-card');
      if (facilityCards.length > 0) {
        gsap.set(facilityCards, {
          opacity: 0,
          y: 50,
          scale: 0.8,
          rotationY: -15
        });

        facilityCards.forEach((card, index) => {
          gsap.to(card, {
            opacity: 1,
            y: 0,
            scale: 1,
            rotationY: 0,
            duration: 0.6,
            delay: index * 0.08,
            ease: 'power2.out'
          });
        });
      }

      // Re-init FAQ accordion setelah navigasi
      initFAQAccordion();
    });

    // FAQ Accordion functionality
    function initFAQAccordion() {
      const faqButtons = document.querySelectorAll('.faq-button');
      faqButtons.forEach(button => {
        // Remove existing listeners
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);

        newButton.addEventListener('click', () => {
          const faqId = newButton.getAttribute('data-faq');
          const content = document.querySelector(`.faq-content[data-content="${faqId}"]`);
          const iconMinus = newButton.querySelector('.faq-icon');
          const iconPlus = newButton.querySelector('.faq-icon-plus');

          // Toggle content
          const isHidden = content.classList.toggle('hidden');

          // Toggle icon visibility (plus when collapsed, minus when expanded)
          if (isHidden) {
            // Collapsed: show plus, hide minus
            if (iconMinus) iconMinus.classList.add('hidden');
            if (iconPlus) iconPlus.classList.remove('hidden');
          } else {
            // Expanded: show minus, hide plus
            if (iconMinus) iconMinus.classList.remove('hidden');
            if (iconPlus) iconPlus.classList.add('hidden');
          }
        });
      });
    }

    // Initialize FAQ on page load
    ready(() => {
      initFAQAccordion();
    });
  })();
</script>