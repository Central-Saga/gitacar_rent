<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Gita Car Rental - Sewa Mobil & Motor Canggu Bali' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFFFFF;
            color: #2D2D2D;
        }

        /* Glassmorphism Navbar */
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        /* Palette Colors */
        .text-primary {
            color: #2FAE9B;
        }

        .bg-primary {
            background-color: #2FAE9B;
        }

        .hover-bg-primary:hover {
            background-color: #248f7f;
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, #2FAE9B 0%, #6ED3C2 100%);
        }

        .hover-bg-primary-gradient:hover {
            background: linear-gradient(135deg, #248f7f 0%, #5bc0b0 100%);
        }

        .text-secondary {
            color: #6C757D;
        }

        .bg-soft {
            background-color: #F5F6F7;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F5F6F7;
        }

        ::-webkit-scrollbar-thumb {
            background: #2FAE9B;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #248f7f;
        }

        /* Dropdown transition */
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s ease;
        }

        .group:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    </style>
</head>

<body class="antialiased selection:bg-[#2FAE9B] selection:text-white">

    <!-- Navbar -->
    <nav id="main-navbar" class="fixed w-full z-50 glass-nav top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('img/logogitacar.png') }}" alt="Gita Car Rental Logo"
                            class="h-10 w-auto transition-transform group-hover:scale-105" />
                        <span class="font-bold text-xl tracking-tight text-[#2D2D2D]">
                            Gita<span class="text-primary">Car</span>
                        </span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex flex-1 justify-center items-center space-x-8">
                    <a href="{{ route('home') }}"
                        class="text-gray-800 hover:text-primary font-medium transition-colors">Home</a>

                    <!-- Dropdown Katalog -->
                    <div class="relative group py-6">
                        <button
                            class="flex items-center text-gray-800 hover:text-primary font-medium transition-colors focus:outline-none">
                            Katalog <i
                                class="fas fa-chevron-down ml-1.5 text-xs transition-transform group-hover:rotate-180"></i>
                        </button>
                        <div
                            class="dropdown-menu absolute left-0 mt-4 w-48 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
                            <div class="py-1">
                                <a href="{{ route('katalog.mobil') }}"
                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-soft hover:text-primary transition-colors">
                                    <i class="fas fa-car w-5 mr-1 text-center text-secondary"></i> Sewa Mobil
                                </a>
                                <a href="{{ route('katalog.motor') }}"
                                    class="block px-4 py-3 text-sm text-gray-700 hover:bg-soft hover:text-primary transition-colors">
                                    <i class="fas fa-motorcycle w-5 mr-1 text-center text-secondary"></i> Sewa Motor
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('about') }}"
                        class="text-gray-800 hover:text-primary font-medium transition-colors">About Us</a>
                    <a href="{{ route('contact') }}"
                        class="text-gray-800 hover:text-primary font-medium transition-colors">Contact
                        Us</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <div class="relative group py-6">
                            <button
                                class="flex items-center gap-2 bg-soft hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-full transition-colors focus:outline-none">
                                <i class="fas fa-user-circle text-lg text-primary"></i>
                                <span class="text-sm font-semibold">{{ Auth::user()->name ?? 'Pengunjung' }}</span>
                                <i
                                    class="fas fa-chevron-down text-[10px] ml-1 text-secondary transition-transform group-hover:rotate-180"></i>
                            </button>
                            <div
                                class="dropdown-menu absolute right-0 mt-4 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
                                <div class="py-1">
                                    <a href="{{ route('home') }}"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-soft hover:text-primary transition-colors">
                                        <i class="fas fa-home w-5 mr-3 text-center text-primary"></i> Beranda
                                    </a>
                                    <a href="{{ route('reservasi') }}"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-soft hover:text-primary transition-colors">
                                        <i class="fas fa-calendar-check w-5 mr-3 text-center text-primary"></i> Reservasi
                                        Saya
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left block px-4 py-3 text-sm text-red-600 font-medium hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 mr-3 text-center"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold text-gray-800 hover:text-primary transition-colors">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="text-sm font-semibold bg-primary-gradient text-white px-5 py-2 rounded-full hover:shadow-lg hover-bg-primary-gradient transition-all transform hover:-translate-y-0.5">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-800 hover:text-primary focus:outline-none p-2">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu"
            class="hidden md:hidden bg-white border-t border-gray-100 shadow-xl absolute w-full left-0">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="{{ route('home') }}"
                    class="block px-3 py-3 rounded-md text-base font-medium text-gray-900 hover:bg-soft hover:text-primary">Home</a>

                <div class="border-t border-gray-100 my-1"></div>
                <div class="px-3 py-2 text-xs font-semibold text-secondary uppercase tracking-wider">Katalog</div>
                <a href="{{ route('katalog.mobil') }}"
                    class="block px-3 py-2 pl-6 rounded-md text-base font-medium text-gray-700 hover:bg-soft hover:text-primary"><i
                        class="fas fa-car mr-2 text-secondary"></i> Mobil</a>
                <a href="{{ route('katalog.motor') }}"
                    class="block px-3 py-2 pl-6 rounded-md text-base font-medium text-gray-700 hover:bg-soft hover:text-primary"><i
                        class="fas fa-motorcycle mr-2 text-secondary"></i> Motor</a>
                <div class="border-t border-gray-100 my-1"></div>

                <a href="{{ route('about') }}"
                    class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:bg-soft hover:text-primary">About
                    Us</a>
                <a href="{{ route('contact') }}"
                    class="block px-3 py-3 rounded-md text-base font-medium text-gray-700 hover:bg-soft hover:text-primary">Contact
                    Us</a>

                <div class="border-t border-gray-100 pt-4 pb-2 mt-2">
                    @auth
                        <a href="{{ route('home') }}"
                            class="block w-full text-left px-4 py-3 mb-2 rounded-xl text-base font-medium text-gray-700 hover:bg-soft"><i
                                class="fas fa-home w-5 mr-2 text-center text-primary"></i> Beranda</a>
                        <a href="{{ route('reservasi') }}"
                            class="block w-full text-left px-4 py-3 mb-2 rounded-xl text-base font-medium text-gray-700 hover:bg-soft"><i
                                class="fas fa-calendar-check w-5 mr-2 text-center text-primary"></i> Reservasi Saya</a>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <button type="submit"
                                class="w-full text-left block px-4 py-3 rounded-xl text-base font-medium text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-5 mr-2 text-center"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="block w-full text-center px-4 py-3 mb-2 rounded-xl text-base font-semibold text-primary border-2 border-[#2FAE9B] hover:bg-soft">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="block w-full text-center px-4 py-3 rounded-xl text-base font-semibold text-white bg-primary-gradient shadow-md">Daftar
                                Sekarang</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('img/logogitacar.png') }}" alt="Gita Car Rental Logo"
                            class="h-8 w-auto" />
                        <span class="font-bold text-xl tracking-tight text-[#2D2D2D]">Gita<span
                                class="text-primary">Car</span></span>
                    </a>
                    <p class="text-secondary leading-relaxed max-w-sm mb-6">
                        Solusi rental mobil dan motor terpercaya di Canggu, Bali. Nikmati perjalanan tanpa ribet dengan
                        armada terawat dan pelayanan terbaik.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/gitacarrental/" target="_blank"
                            class="w-10 h-10 rounded-full bg-soft flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.facebook.com/gitacarrentalbali/" target="_blank"
                            class="w-10 h-10 rounded-full bg-soft flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4 text-[#2D2D2D]">Layanan Kami</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('katalog.mobil') }}"
                                class="text-secondary hover:text-primary transition-colors">Sewa
                                Mobil</a></li>
                        <li><a href="{{ route('katalog.motor') }}"
                                class="text-secondary hover:text-primary transition-colors">Sewa
                                Motor</a></li>
                        <li><a href="{{ request()->routeIs('home') ? '#syarat' : url('/#syarat') }}"
                                class="text-secondary hover:text-primary transition-colors">Syarat &
                                Ketentuan</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4 text-[#2D2D2D]">Hubungi Kami</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-primary mt-1 mr-3"></i>
                            <span class="text-secondary text-sm">Jl. Tanah Barak No.47a, Canggu, Kec. Kuta Utara, Kab.
                                Badung, Bali 80351</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fab fa-whatsapp text-primary mr-3"></i>
                            <a href="https://wa.me/628123929934" target="_blank"
                                class="text-secondary hover:text-primary transition-colors">0812-3929-934</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-primary mr-3"></i>
                            <a href="mailto:info@gitacarrental.com"
                                class="text-secondary hover:text-primary transition-colors">info@gitacarrental.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-secondary">
                <p>&copy; {{ date('Y') }} Gita Car Rental. All rights reserved.</p>
                <div class="mt-4 md:mt-0 space-x-4">
                    <a href="#" class="hover:text-primary transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-primary transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize AOS
            AOS.init({
                once: true,
                offset: 50,
                duration: 800,
                easing: 'ease-out-cubic',
            });

            // Mobile menu toggle
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const icon = btn.querySelector('i');

            if (btn && menu) {
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                    if (menu.classList.contains('hidden')) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    } else {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    }
                });
            }

            // Close mobile menu when clicking a link
            const mobileLinks = menu.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                });
            });

            // Navbar scroll effect
            const navbar = document.getElementById('main-navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbar.classList.add('shadow-md');
                } else {
                    navbar.classList.remove('shadow-md');
                }
            });
        });

        // Re-init AOS on Livewire navigate
        document.addEventListener('livewire:navigating', () => {
            // cleanup if needed
        });
        document.addEventListener('livewire:navigated', () => {
            if (typeof AOS !== 'undefined') {
                setTimeout(() => AOS.refresh(), 100);
            }
        });
    </script>
</body>

</html>