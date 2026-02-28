<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-[#F8FAFB]">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            @can('kelola_user')
                <flux:sidebar.group :heading="__('User Management')" class="grid mt-4">
                    <flux:sidebar.item icon="users" :href="route('admin.users.index')"
                        :current="request()->routeIs('admin.users.*')"
                        class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                        wire:navigate>
                        {{ __('Users') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('admin.roles.index')"
                        :current="request()->routeIs('admin.roles.*')"
                        class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                        wire:navigate>
                        {{ __('Roles') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endcan

            @can('kelola_pelanggan')
                <flux:sidebar.group :heading="__('Manajemen Pelanggan')" class="grid mt-4">
                    <flux:sidebar.item icon="users" :href="route('admin.pelanggans.index')"
                        :current="request()->routeIs('admin.pelanggans.*')"
                        class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                        wire:navigate>
                        {{ __('Pelanggan') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endcan

            @can('kelola_kendaraan')
                <flux:sidebar.group :heading="__('Manajemen Armada')" class="grid mt-4">
                    <flux:sidebar.item icon="truck" :href="route('admin.kendaraan.index')"
                        :current="request()->routeIs('admin.kendaraan.*')"
                        class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                        wire:navigate>
                        {{ __('Kendaraan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="key" :href="route('admin.kendaraan-units.index')"
                        :current="request()->routeIs('admin.kendaraan-units.*')"
                        class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                        wire:navigate>
                        {{ __('Unit Kendaraan') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endcan

            @can('kelola_pemesanan')
                <flux:sidebar.group :heading="__('Manajemen Pemesanan')" class="grid mt-4">
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.pemesanan.index')"
                        :current="request()->routeIs('admin.pemesanan.*')"
                        class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                        wire:navigate>
                        {{ __('Data Pemesanan') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endcan

            @can('kelola_diskon')
                <flux:sidebar.group :heading="__('Manajemen Diskon')" class="grid mt-4">
                    <flux:sidebar.item icon="ticket" :href="route('admin.promo.index')"
                        :current="request()->routeIs('admin.promo.*')"
                        class="text-zinc-600 hover:text-primary hover:bg-primary/5 data-[current]:bg-primary data-[current]:text-white transition-colors duration-200 rounded-lg mx-2"
                        wire:navigate>
                        {{ __('Diskon / Promo') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endcan
        </flux:sidebar.nav>

        <flux:spacer />

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal:confirm', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    title: payload.title,
                    text: payload.text,
                    icon: payload.icon,
                    showCancelButton: true,
                    confirmButtonColor: '#10b981', // green / primary
                    cancelButtonColor: '#ef4444', // red
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(payload.method, { id: payload.id });
                    }
                });
            });

            Livewire.on('swal:toast', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: payload.icon,
                    title: payload.title
                });
            });
        });
    </script>
</body>

</html>