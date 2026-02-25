<?php

use function Livewire\Volt\{
    layout, title, state, mount
};
use App\Models\Kendaraan;
use App\Models\KendaraanUnit;

layout('layouts.app');
title('Tambah Unit Kendaraan');

state([
    'kendaraanList' => [],
    'kendaraan_id' => '',
    'nomor_plat' => '',
    'tahun' => '',
    'status_unit' => 'tersedia',
]);

mount(function () {
    $this->kendaraanList = Kendaraan::orderBy('nama_kendaraan')->get();
});

$save = function() {
    $this->validate([
        'kendaraan_id' => 'required|exists:kendaraans,id',
        'nomor_plat' => 'required|string|max:20|unique:kendaraan_units,nomor_plat',
        'tahun' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
        'status_unit' => 'required|in:tersedia,disewa,maintenance,nonaktif',
    ], [
        'kendaraan_id.required' => 'Kendaraan wajib dipilih',
        'nomor_plat.required' => 'Nomor plat wajib diisi',
        'nomor_plat.unique' => 'Nomor plat sudah terdaftar sebelumnya',
        'tahun.required' => 'Tahun wajib diisi',
        'tahun.digits' => 'Tahun harus berupa 4 digit angka',
    ]);

    KendaraanUnit::create([
        'kendaraan_id' => $this->kendaraan_id,
        'nomor_plat' => strtoupper($this->nomor_plat),
        'tahun' => $this->tahun,
        'status_unit' => $this->status_unit,
    ]);

    session()->flash('message', 'Unit kendaraan berhasil ditambahkan!');
    return $this->redirect(route('admin.kendaraan-units.index'));
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Unit Kendaraan</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                            Daftarkan plat nomor baru untuk kendaraan yang tersedia
                        </p>
                    </div>
                    <a href="{{ route('admin.kendaraan-units.index') }}"
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
                    <div x-data="{ open: true }" class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left focus:outline-none">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                                <div class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                    <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                </div>
                                Informasi Unit
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="kendaraan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Kendaraan <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="kendaraan_id" id="kendaraan_id"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('kendaraan_id') border-red-500 @endif">
                                        <option value="">-- Pilih Kendaraan Master --</option>
                                        @foreach($kendaraanList as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kendaraan }} ({{ ucfirst($k->jenis_kendaraan) }})</option>
                                        @endforeach
                                    </select>
                                    @error('kendaraan_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nomor_plat" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nomor Plat <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="nomor_plat" type="text" id="nomor_plat"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm uppercase @error('nomor_plat') border-red-500 @endif"
                                        placeholder="Contoh: B 1234 CD">
                                    @error('nomor_plat')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tahun Pembuatan <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="tahun" type="number" id="tahun"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('tahun') border-red-500 @endif"
                                        placeholder="Contoh: 2022">
                                    @error('tahun')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="status_unit" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status Unit <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="status_unit" id="status_unit"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('status_unit') border-red-500 @endif">
                                        <option value="tersedia">Tersedia</option>
                                        <option value="disewa">Sedang Disewa</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    @error('status_unit')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('admin.kendaraan-units.index') }}"
                            wire:navigate
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors disabled:opacity-50">
                            Simpan Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
