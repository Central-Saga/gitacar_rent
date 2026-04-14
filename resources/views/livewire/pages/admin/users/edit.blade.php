<?php

use function Livewire\Volt\{
    layout, title, state, mount
};
use App\Models\User;
use Spatie\Permission\Models\Role;

layout('layouts.app');
title('Edit User');

state([
    'user' => null,
    'name' => '',
    'email' => '',
    'telepon' => '',
    'password' => '',
    'password_confirmation' => '',
    'selectedRoles' => [],
]);

mount(function(User $user) {
    $this->user = $user;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->telepon = $user->telepon;
    $this->selectedRoles = $user->roles->pluck('name')->toArray();
    $this->roles = Role::orderBy('name')->get();
});

$save = function() {
    $this->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
        'telepon' => 'nullable|string|max:30',
        'password' => 'nullable|string|min:8|confirmed',
    ], [
        'name.required' => 'Semua data wajib diisi',
        'email.required' => 'Semua data wajib diisi',
        'email.email' => 'Semua data wajib diisi',
        'password.min' => 'Password harus minimal 8 karakter',
        'password.confirmed' => 'Password dan konfirmasi password tidak sesuai',
    ]);

    $updateData = [
        'name' => $this->name,
        'email' => $this->email,
        'telepon' => $this->telepon ?: null,
    ];

    if (!empty($this->password)) {
        $updateData['password'] = bcrypt($this->password);
    }

    $this->user->update($updateData);
    $this->user->syncRoles($this->selectedRoles);

    session()->flash('message', 'User berhasil diperbarui!');
    return $this->redirect(route('admin.users.index'));
};

?>

<div class="w-full max-w-none">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
                        <p class="mt-2 text-sm text-gray-600 font-medium">
                            Edit user "{{ $user->name }}" untuk sistem Gita Car Rental
                        </p>
                    </div>
                    <a href="{{ route('admin.users.index') }}"
                        wire:navigate
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                <form wire:submit="save" class="space-y-8">
                        <!-- Basic Information -->
                        <div x-data="{ open: true }" class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left focus:outline-none">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                        <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    </div>
                                    Data Diri Pengguna
                                </h3>
                                <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input wire:model="name" type="text" id="name"
                                            class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('name') border-red-500 @endif"
                                            placeholder="Masukkan nama lengkap user">
                                        @error('name')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="email"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input wire:model="email" type="email" id="email"
                                            class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('email') border-red-500 @endif"
                                            placeholder="user@example.com">
                                        @error('email')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="telepon"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            Telepon
                                        </label>
                                        <input wire:model="telepon" type="text" id="telepon"
                                            class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('telepon') border-red-500 @endif"
                                            placeholder="08xxxxxxxxxx">
                                        @error('telepon')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <label for="password"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            Password Baru <span class="text-gray-500 text-xs">(Opsional)</span>
                                        </label>
                                        <input wire:model="password" type="password" id="password"
                                            class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('password') border-red-500 @endif"
                                            placeholder="Kosongkan jika tidak ingin mengubah password">
                                        @error('password')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700 mb-2">
                                            Konfirmasi Password Baru
                                        </label>
                                        <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                            class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('password_confirmation') border-red-500 @endif"
                                            placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Current User Info -->
                        <div x-data="{ open: false }" class="bg-primaryLight/10 border border-primaryLight/20 rounded-2xl p-6 transition-all duration-300">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left focus:outline-none">
                                <h4 class="text-sm font-bold text-primaryDark">Informasi User</h4>
                                <svg class="w-5 h-5 text-primaryDark transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-primary">
                                    <div class="bg-white rounded-xl p-3 border border-primaryLight/20 shadow-sm">
                                        <p class="font-semibold text-primaryDark mb-1">Dibuat</p>
                                        <p>{{ $user->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-primaryLight/20 shadow-sm">
                                        <p class="font-semibold text-primaryDark mb-1">Diperbarui</p>
                                        <p>{{ $user->updated_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-primaryLight/20 shadow-sm">
                                        <p class="font-semibold text-primaryDark mb-1">Roles</p>
                                        <p>{{ $user->roles->count() }} role</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div x-data="{ open: false }" class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left focus:outline-none">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                        <div class="w-8 h-8 bg-primary/20 rounded-xl flex items-center justify-center border border-primary/30">
                                            <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </div>
                                        Roles
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Pilih role yang akan diberikan kepada user ini
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                                <div class="bg-white rounded-xl p-6 border border-gray-200">
                                    <div class="flex flex-col space-y-3">
                                        @foreach($this->roles as $role)
                                        <label
                                            class="flex items-center p-4 bg-white rounded-xl border border-inputBorder hover:border-primaryLight hover:bg-primaryLight/10 cursor-pointer transition-all duration-200">
                                            <input wire:model="selectedRoles" type="checkbox" value="{{ $role->name }}"
                                                class="h-5 w-5 text-primary focus:ring-primary border-inputBorder rounded-lg">
                                            <span class="ml-3 text-sm font-medium text-gray-900">
                                                {{ $role->name }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>

                                    @if($this->roles->count() === 0)
                                    <div class="text-center py-12">
                                        <div class="mx-auto h-24 w-24 bg-gray-200 rounded-full flex items-center justify-center mb-6">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak ada role</h3>
                                        <p class="text-sm text-gray-600">Tidak ada role yang tersedia saat ini</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}"
                                wire:navigate
                                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-8 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors">
                                Update User
                            </button>
                        </div>
                    </form>
            </div>
</div>
