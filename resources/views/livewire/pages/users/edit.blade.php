<?php

use function Livewire\Volt\{
    layout, title, state, mount
};
use App\Models\User;
use Spatie\Permission\Models\Role;

layout('components.layouts.admin');
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

<div class="min-h-screen bg-[#FAFAF7]">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
                        <p class="mt-2 text-sm text-gray-600 font-medium">
                            Edit user "{{ $user->name }}" untuk sistem Watugangga Riverside Guest House
                        </p>
                    </div>
                    <a href="{{ route('admin.users.index') }}"
                        wire:navigate
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1D2D20] hover:bg-[#162217] text-white text-sm font-semibold rounded-xl transition-all duration-300">
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
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6">
                                Informasi Dasar
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="name" type="text" id="name"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm @error('name') border-red-500 @endif"
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
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm @error('email') border-red-500 @endif"
                                        placeholder="user@example.com">
                                    @error('email')
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
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm @error('password') border-red-500 @endif"
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
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm @error('password_confirmation') border-red-500 @endif"
                                        placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>

                        <!-- Data Diri -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6">
                                Data Diri Pengguna
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="telepon"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Telepon
                                    </label>
                                    <input wire:model="telepon" type="text" id="telepon"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm @error('telepon') border-red-500 @endif"
                                        placeholder="08xxxxxxxxxx">
                                    @error('telepon')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Current User Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                            <div class="flex items-center mb-4">
                                <h4 class="text-sm font-bold text-blue-800">Informasi User</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700">
                                <div class="bg-white rounded-xl p-3 border border-blue-200">
                                    <p class="font-semibold text-blue-800 mb-1">Dibuat</p>
                                    <p>{{ $user->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-blue-200">
                                    <p class="font-semibold text-blue-800 mb-1">Diperbarui</p>
                                    <p>{{ $user->updated_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-blue-200">
                                    <p class="font-semibold text-blue-800 mb-1">Roles</p>
                                    <p>{{ $user->roles->count() }} role</p>
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-6">
                                Roles
                            </h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Pilih role yang akan diberikan kepada user ini
                            </p>

                            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($this->roles as $role)
                                    <label
                                        class="flex items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200">
                                        <input wire:model="selectedRoles" type="checkbox" value="{{ $role->name }}"
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-lg">
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

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}"
                                wire:navigate
                                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-8 py-3 bg-[#1D2D20] hover:bg-[#162217] text-white font-semibold rounded-xl transition-colors">
                                Update User
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
