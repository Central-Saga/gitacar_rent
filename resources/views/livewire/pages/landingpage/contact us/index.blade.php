<?php

use function Livewire\Volt\{layout, title, state, mount};

layout('components.layouts.landing');
title('Contact Us - Watugangga Riverside Guest House');

?>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="text-slate-800">
  <!-- ===== CONTACT US CTA ===== -->
  <section id="contact-cta" class="scroll-mt-[var(--nav-h)] relative min-h-[calc(100svh-var(--nav-h))] md:min-h-[calc(100dvh-var(--nav-h))] pt-[calc(var(--nav-h)+2rem)] bg-[#FBFAF6] overflow-hidden">
    <div class="container mx-auto px-4 h-full flex items-center">
      <div class="text-center max-w-3xl mx-auto w-full py-16 md:py-24 mt-12 md:mt-24">
        <p class="text-sm md:text-base font-sans uppercase tracking-wide text-slate-600 mb-4" data-aos="fade-up" data-aos-duration="800">
          REACH OUT to OUR TEAM
        </p>
        <h1 class="text-1xl md:text-2xl lg:text-3xl font-serif font-semibold text-slate-900 mb-6"
          style="font-family: 'Playfair Display', 'Cormorant Garamond', serif; font-weight: 500;"
          data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
          Contact Us
        </h1>
        <p class="text-sm md:text-base text-slate-700 leading-relaxed mb-8"
          data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
          For bookings or more information, reach out to us with the enquiry form and our reservations team will get back to you as soon as possible.
        </p>
        <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
          <a href="https://wa.me/6287862173133"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 rounded-lg bg-green-500 hover:bg-green-600 
                    text-white px-5 py-2.5 font-medium text-sm 
                    transition-all duration-300 shadow-md hover:scale-105">
            <i class="fab fa-whatsapp text-base"></i>
            <span>WhatsApp Us</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== FOOTER-LIKE INFORMATION ===== -->
  <section id="contact-footer" class="bg-[#F5F5F5] py-16 md:py-20">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-5 gap-12 max-w-7xl mx-auto">
        <!-- Left Column: Reservation Info (3/5 width - 60%) -->
        <div class="lg:col-span-3" data-aos="fade-right" data-aos-duration="800">
          <h2 class="text-lg md:text-1xl font-bold text-slate-900 mb-4">
            Watugangga Riverside Guest House Reservasion
          </h2>
          <p class="text-sm md:text-base text-slate-700 leading-relaxed mb-6 max-w-2xl">
            Our reservations team are always available to help. From villa bookings and travel planning to itineraries and experiences, our team is here to assist you.
          </p>

          <!-- Divider -->
          <div class="border-t border-slate-300 my-6 max-w-2xl"></div>

          <!-- Address -->
          <p class="text-base text-slate-700 mb-8 max-w-2xl">
            Gg. Salak No.Br, Pererenan, Kec. Mengwi, Kabupaten Badung, Bali 80351
          </p>

          <!-- Contact Details (2 sub-columns) -->
          <div class="grid md:grid-cols-2 gap-8 max-w-2xl">
            <!-- Left Sub-column -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-envelope text-slate-600"></i>
                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide">BY EMAIL</p>
              </div>
              <a href="mailto:watuganggariverside@gmail.com"
                class="text-base text-slate-900 hover:text-slate-700 transition-colors duration-300 block mb-6">
                watuganggariverside@gmail.com
              </a>

              <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-phone text-slate-600"></i>
                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide">BY WHATSAPP</p>
              </div>
              <a href="https://wa.me/6287862173133"
                target="_blank"
                rel="noopener noreferrer"
                class="text-base text-slate-900 hover:text-slate-700 transition-colors duration-300 block">
                087862173133
              </a>
            </div>

            <!-- Right Sub-column -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-globe text-slate-600"></i>
                <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide">VISIT WEBSITE</p>
              </div>
              <a href="https://watuganggariverside.online"
                target="_blank"
                rel="noopener noreferrer"
                class="text-base text-slate-900 hover:text-slate-700 transition-colors duration-300 block mb-6">
                watuganggariverside.online
              </a>

              <p class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-3">FOLLOW OUR SOCIALS</p>
              <div class="flex items-center gap-4">
                <a href="#"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-slate-900 hover:text-slate-700 transition-colors duration-300">
                  <i class="fab fa-instagram text-2xl"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Stay Updated (2/5 width - 40%) -->
        <div class="lg:col-span-2" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
          <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-4">
            Stay Updated
          </h2>
          <p class="text-sm md:text-base text-slate-700 leading-relaxed mb-6">
            Never miss out — stay up to date with the latest updates, exclusive deals and special promotions.
          </p>

          <!-- Divider (align dengan divider di left column) -->
          <div class="border-t border-slate-300 mt-6 max-w-md"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Floating WhatsApp Button -->
  <a href="https://wa.me/6287862173133" 
     target="_blank" 
     rel="noopener noreferrer"
     class="fixed bottom-[50px] right-[75px] z-50 w-14 h-14 bg-[#25D366] hover:bg-[#20BA5A] rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 group"
     aria-label="Contact us on WhatsApp">
    <i class="fab fa-whatsapp text-white text-2xl"></i>
    <span class="absolute -top-12 right-0 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap pointer-events-none">
      Chat with us
    </span>
  </a>
</div>

<script>
  (function() {
    const ready = (cb) => (document.readyState === 'loading') ?
      document.addEventListener('DOMContentLoaded', cb) :
      cb();

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
          const navH = 84; // Fixed navbar height
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
    });
  })();
</script>