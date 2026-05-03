<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden w-full">

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

        /* Hide scrollbar for horizontal scrolling */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased selection:bg-[#2FAE9B] selection:text-white pb-20 md:pb-0 overflow-x-hidden w-full relative">

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

                <!-- Mobile Auth Button (Top Right) -->
                <div class="md:hidden flex items-center">
                    @auth
                        <div class="relative group">
                            <button
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-soft hover:bg-gray-200 text-gray-800 transition-colors focus:outline-none">
                                <i class="fas fa-user-circle text-2xl text-primary"></i>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
                                <div class="py-1">
                                    <a href="{{ route('reservasi') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-soft hover:text-primary transition-colors">
                                        <i class="fas fa-calendar-check w-5 mr-2 text-center text-primary"></i> Reservasi
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <button type="submit" class="w-full text-left block px-4 py-3 text-sm text-red-600 font-medium hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 mr-2 text-center"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-soft hover:bg-gray-200 text-primary transition-colors">
                            <i class="fas fa-user-circle text-2xl"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Navigation -->
    <div x-data="{ showKatalog: false, isVisible: true, lastScrollY: 0 }"
        @scroll.window="
            if (window.scrollY > lastScrollY && window.scrollY > 100) { 
                isVisible = false; 
                showKatalog = false; 
            } else if (window.scrollY < lastScrollY) { 
                isVisible = true; 
            }
            lastScrollY = window.scrollY;
        "
        class="md:hidden">
        <div :class="isVisible ? 'translate-y-0' : 'translate-y-[150%]'"
            class="fixed bottom-4 inset-x-0 mx-auto z-50 w-[95%] max-w-sm transition-transform duration-300 ease-in-out">
            <div class="bg-[#1a1a1a] rounded-full shadow-2xl px-2 py-2 flex items-center justify-between border border-gray-800 relative z-50">
                <a href="{{ route('home') }}" class="flex-1 flex flex-col items-center justify-center py-1 rounded-full {{ request()->routeIs('home') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <i class="fas fa-home mb-1 text-lg"></i>
                    <span class="text-[10px] font-medium">Home</span>
                </a>
                <button @click="showKatalog = !showKatalog" class="flex-1 flex flex-col items-center justify-center py-1 rounded-full {{ request()->routeIs('katalog.*') ? 'text-white' : 'text-gray-400 hover:text-white' }} focus:outline-none">
                    <i class="fas fa-car mb-1 text-lg"></i>
                    <span class="text-[10px] font-medium">Katalog</span>
                </button>
                <a href="{{ route('contact') }}" class="flex-1 flex flex-col items-center justify-center py-1 rounded-full {{ request()->routeIs('contact') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                    <i class="fas fa-phone-alt mb-1 text-lg"></i>
                    <span class="text-[10px] font-medium">Contact</span>
                </a>
                @auth
                    <a href="{{ route('reservasi') }}" class="flex-1 flex flex-col items-center justify-center py-1 rounded-full {{ request()->routeIs('reservasi') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                        <i class="fas fa-calendar-check mb-1 text-lg"></i>
                        <span class="text-[10px] font-medium">Reservasi</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex-1 flex flex-col items-center justify-center py-1 rounded-full {{ request()->routeIs('login') ? 'text-white' : 'text-gray-400 hover:text-white' }}">
                        <i class="fas fa-user mb-1 text-lg"></i>
                        <span class="text-[10px] font-medium">Sign In</span>
                    </a>
                @endauth
            </div>
            
            <!-- Katalog Pop up (Bottom Sheet) -->
            <div x-show="showKatalog" style="display: none;"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-full"
                @click.away="showKatalog = false"
                class="absolute bottom-[110%] left-0 w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-2 z-50">
                <div class="flex items-center justify-between px-3 py-2 border-b border-gray-100 mb-2">
                    <span class="font-bold text-gray-800 text-sm">Pilih Katalog</span>
                    <button @click="showKatalog = false" class="text-gray-400 hover:text-gray-600 focus:outline-none"><i class="fas fa-times"></i></button>
                </div>
                <a href="{{ route('katalog.mobil') }}" class="flex items-center p-3 rounded-xl hover:bg-soft transition-colors mb-1">
                    <div class="w-10 h-10 bg-[#2FAE9B]/10 text-primary rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-car text-lg"></i>
                    </div>
                    <span class="font-semibold text-gray-700 text-sm">Sewa Mobil</span>
                </a>
                <a href="{{ route('katalog.motor') }}" class="flex items-center p-3 rounded-xl hover:bg-soft transition-colors">
                    <div class="w-10 h-10 bg-[#2FAE9B]/10 text-primary rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-motorcycle text-lg"></i>
                    </div>
                    <span class="font-semibold text-gray-700 text-sm">Sewa Motor</span>
                </a>
            </div>
        </div>
        
        <!-- Backdrop -->
        <div x-show="showKatalog" style="display: none;"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/40 z-40"></div>
    </div>

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