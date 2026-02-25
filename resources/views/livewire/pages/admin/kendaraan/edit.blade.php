<?php

use function Livewire\Volt\{
    layout, title, state, mount, usesFileUploads
};
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Storage;

layout('layouts.app');
title('Edit Kendaraan');

usesFileUploads();

state([
    'kendaraan' => null,
    'nama_kendaraan' => '',
    'jenis_kendaraan' => 'mobil',
    'harga_sewa_per_hari' => '',
    'deskripsi' => '',
    'foto' => null,
    'existing_foto' => null,
]);

mount(function (Kendaraan $kendaraan) {
    $this->kendaraan = $kendaraan;
    $this->nama_kendaraan = $kendaraan->nama_kendaraan;
    $this->jenis_kendaraan = $kendaraan->jenis_kendaraan;
    $this->harga_sewa_per_hari = $kendaraan->harga_sewa_per_hari;
    $this->deskripsi = $kendaraan->deskripsi;
    $this->existing_foto = $kendaraan->foto;
});

$save = function() {
    $this->validate([
        'nama_kendaraan' => 'required|string|max:255',
        'jenis_kendaraan' => 'required|in:motor,mobil',
        'harga_sewa_per_hari' => 'required|numeric|min:0',
        'deskripsi' => 'nullable|string',
        'foto' => 'nullable|image|max:2048',
    ], [
        'nama_kendaraan.required' => 'Nama kendaraan wajib diisi',
        'jenis_kendaraan.in' => 'Jenis kendaraan tidak valid',
        'harga_sewa_per_hari.required' => 'Harga sewa wajib diisi',
        'harga_sewa_per_hari.numeric' => 'Harga sewa harus berupa angka',
        'foto.image' => 'File harus berupa gambar',
        'foto.max' => 'Ukuran gambar maksimal 2MB',
    ]);

    $fotoPath = $this->kendaraan->foto;
    
    if ($this->foto) {
        if ($this->kendaraan->foto && Storage::disk('public')->exists($this->kendaraan->foto)) {
            Storage::disk('public')->delete($this->kendaraan->foto);
        }
        $fotoPath = $this->foto->store('kendaraan', 'public');
    }

    $this->kendaraan->update([
        'nama_kendaraan' => $this->nama_kendaraan,
        'jenis_kendaraan' => $this->jenis_kendaraan,
        'harga_sewa_per_hari' => $this->harga_sewa_per_hari,
        'deskripsi' => $this->deskripsi,
        'foto' => $fotoPath,
    ]);

    session()->flash('message', 'Master kendaraan berhasil diperbarui!');
    return $this->redirect(route('admin.kendaraan.index'));
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Master Kendaraan</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                            Perbarui katalog kendaraan ({{ $kendaraan->nama_kendaraan }})
                        </p>
                    </div>
                    <a href="{{ route('admin.kendaraan.index') }}"
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
                                    <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                Informasi Kendaraan
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nama_kendaraan" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Kendaraan <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="nama_kendaraan" type="text" id="nama_kendaraan"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('nama_kendaraan') border-red-500 @endif">
                                    @error('nama_kendaraan')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-2">
                                        Jenis Kendaraan <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="jenis_kendaraan" id="jenis_kendaraan"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('jenis_kendaraan') border-red-500 @endif">
                                        <option value="mobil">Mobil</option>
                                        <option value="motor">Motor</option>
                                    </select>
                                    @error('jenis_kendaraan')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="harga_sewa_per_hari" class="block text-sm font-medium text-gray-700 mb-2">
                                        Harga Sewa per Hari (Rp) <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="harga_sewa_per_hari" type="number" id="harga_sewa_per_hari" min="0"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('harga_sewa_per_hari') border-red-500 @endif">
                                    @error('harga_sewa_per_hari')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi (Opsional)
                                </label>
                                <textarea wire:model="deskripsi" id="deskripsi" rows="3"
                                    class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm @error('deskripsi') border-red-500 @endif"></textarea>
                                @error('deskripsi')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Foto Baru (Opsional, timpa foto lama)
                                </label>
                                
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl relative overflow-hidden bg-white">
                                    <div class="space-y-1 text-center">
                                        @if ($foto)
                                            <img src="{{ $foto->temporaryUrl() }}" class="mx-auto h-48 w-auto rounded-lg object-cover mb-4">
                                            <p class="text-sm font-medium text-green-600 mb-2">Foto siap diupload.</p>
                                        @elseif ($existing_foto)
                                            <img src="{{ Storage::url($existing_foto) }}" class="mx-auto h-48 w-auto rounded-lg object-cover mb-4 border border-gray-200 shadow-sm">
                                            <p class="text-sm font-medium text-gray-500 mb-2">Foto saat ini.</p>
                                        @else
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        @endif
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primaryDark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                                <span>Pilih file foto baru</span>
                                                <input id="foto" wire:model="foto" name="foto" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG up to 2MB
                                        </p>
                                        <div wire:loading wire:target="foto" class="text-sm text-primary mt-2">
                                            Mengupload...
                                        </div>
                                    </div>
                                </div>
                                @error('foto')
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

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('admin.kendaraan.index') }}"
                            wire:navigate
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors disabled:opacity-50">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
