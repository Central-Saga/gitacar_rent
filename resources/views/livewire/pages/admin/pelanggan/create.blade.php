<?php

use function Livewire\Volt\{
    layout, title, state, usesFileUploads
};
use App\Models\User;
use App\Models\Pelanggan;

layout('layouts.app');
title('Tambah Pelanggan');
usesFileUploads();

state([
    'nama' => '',
    'email' => '',
    'no_telp' => '',
    'alamat' => '',
    'nik' => '',
    'foto_ktp' => null,
    'password' => '',
    'password_confirmation' => '',
]);

$save = function() {
    $this->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'no_telp' => 'nullable|string|max:30',
        'alamat' => 'nullable|string',
        'nik' => 'nullable|string|max:30',
        'foto_ktp' => 'nullable|image|max:2048', // 2MB Max
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $this->nama,
        'email' => $this->email,
        'password' => bcrypt($this->password),
    ]);

    $user->assignRole('pelanggan');

    $fotoPath = null;
    if ($this->foto_ktp) {
        $fotoPath = $this->foto_ktp->store('ktp', 'public');
    }

    $user->pelanggan()->create([
        'nama' => $this->nama,
        'email' => $this->email,
        'no_telp' => $this->no_telp,
        'alamat' => $this->alamat,
        'nik' => $this->nik,
        'foto_ktp' => $fotoPath,
    ]);

    session()->flash('message', 'Pelanggan berhasil ditambahkan!');
    return $this->redirect(route('admin.pelanggans.index'));
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tambah Pelanggan Baru</h1>
                        <p class="mt-2 text-sm text-gray-600 font-medium">Buat pelanggan baru beserta akun usernya.</p>
                    </div>
                    <a href="{{ route('admin.pelanggans.index') }}"
                        wire:navigate
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
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
                                Data Pelanggan
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" x-collapse class="mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                                    <input wire:model="nama" type="text" id="nama" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('nama') border-red-500 @endif">
                                    @error('nama') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input wire:model="email" type="email" id="email" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('email') border-red-500 @endif">
                                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-2">No. Telp</label>
                                    <input wire:model="no_telp" type="text" id="no_telp" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('no_telp') border-red-500 @endif">
                                    @error('no_telp') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                                    <input wire:model="nik" type="text" id="nik" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('nik') border-red-500 @endif">
                                    @error('nik') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                    <textarea wire:model="alamat" id="alamat" rows="3" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('alamat') border-red-500 @endif"></textarea>
                                    @error('alamat') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="foto_ktp" class="block text-sm font-medium text-gray-700 mb-2">Foto KTP (Maks 2MB)</label>
                                    <input wire:model="foto_ktp" type="file" id="foto_ktp" accept="image/*" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('foto_ktp') border-red-500 @endif">
                                    @if ($foto_ktp)
                                        <div class="mt-4">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                                            <img src="{{ $foto_ktp->temporaryUrl() }}" class="h-32 rounded-lg object-cover border border-gray-200">
                                        </div>
                                    @endif
                                    @error('foto_ktp') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Details -->
                    <div x-data="{ open: true }" class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left focus:outline-none">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                <div class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                    <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                Keamanan Akun
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" x-collapse class="mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input wire:model="password" type="password" id="password" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('password') border-red-500 @endif">
                                    @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                                    <input wire:model="password_confirmation" type="password" id="password_confirmation" class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('password_confirmation') border-red-500 @endif">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('admin.pelanggans.index') }}"
                            wire:navigate
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors">
                            Simpan Pelanggan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
