<?php

use function Livewire\Volt\{
    layout,
    title,
    state,
    mount,
    with,
    updated,
    usesPagination
};
use App\Models\Kendaraan;

layout('layouts.app');
title('Kelola Kendaraan');

usesPagination();

state([
    'search' => '',
]);

state(['search'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

updated([
    'search' => fn() => $this->resetPage(),
]);

with(function () {
    try {
        $query = Kendaraan::query()
            ->withCount([
                'units as total_unit',
                'units as unit_tersedia' => function ($query) {
                    $query->where('status_unit', 'tersedia');
                }
            ]);

        if ($this->search) {
            $query->where('nama_kendaraan', 'like', '%' . $this->search . '%')
                ->orWhere('jenis_kendaraan', 'like', '%' . $this->search . '%');
        }

        $kendaraans = $query->orderBy('created_at', 'desc')->paginate(10);

        return [
            'kendaraans' => $kendaraans,
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading kendaraan data', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [
            'kendaraans' => collect(),
        ];
    }
});

$delete = function (Kendaraan $kendaraan) {
    if ($kendaraan->foto) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($kendaraan->foto);
    }
    $kendaraan->delete();

    session()->flash('message', 'Kendaraan berhasil dihapus!');
    $this->resetPage();
};

?>

<div>
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Kendaraan</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Katalog master data
                            kendaraan</p>
                    </div>
                    <a href="{{ route('admin.kendaraan.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300"
                        wire:navigate>
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Kendaraan</span>
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-8">
                <input wire:model.live="search" type="text" placeholder="Cari kendaraan (nama / jenis)..."
                    class="block w-full px-4 py-3 rounded-2xl bg-white text-textDark placeholder-textGray border border-inputBorder focus:ring-2 focus:ring-primary focus:outline-none text-sm font-medium">
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div
                    class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-lg dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-700/30 dark:text-green-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            <!-- Table -->
            <div class="relative">
                <div class="relative bg-white shadow-sm rounded-2xl overflow-hidden border border-inputBorder">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 border-b border-inputBorder">
                                <tr>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Kendaraan</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Jenis</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Harga/Hari</th>
                                    <th
                                        class="px-8 py-6 text-center text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Total Unit</th>
                                    <th
                                        class="px-8 py-6 text-center text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Tersedia</th>
                                    <th
                                        class="px-8 py-6 text-right text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($kendaraans as $kendaraan)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-16 w-16 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 flex items-center justify-center">
                                                    @if($kendaraan->foto)
                                                        <img src="{{ $kendaraan->foto_url }}"
                                                            alt="{{ $kendaraan->nama_kendaraan }}"
                                                            onerror="this.onerror=null; this.src='{{ $kendaraan->placeholder_foto_url }}';"
                                                            class="h-full w-full object-cover">
                                                    @else
                                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="ml-5">
                                                    <div class="text-sm font-bold text-textDark">
                                                        {{ $kendaraan->nama_kendaraan }}</div>
                                                    <div class="text-xs text-textGray mt-1 max-w-xs truncate">
                                                        {{ Str::limit($kendaraan->deskripsi, 50) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="px-8 py-6 whitespace-nowrap text-sm font-semibold text-textDark capitalize">
                                            {{ $kendaraan->jenis_kendaraan }}
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-sm text-textGray font-medium">
                                            Rp {{ number_format($kendaraan->harga_sewa_per_hari, 0, ',', '.') }}
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-center text-sm font-bold text-textDark">
                                            {{ $kendaraan->total_unit }}
                                        </td>
                                        <td
                                            class="px-8 py-6 whitespace-nowrap text-center text-sm font-bold text-green-600">
                                            {{ $kendaraan->unit_tersedia }}
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-3">
                                                <a href="{{ route('admin.kendaraan.edit', $kendaraan) }}"
                                                    class="group p-2 text-primary hover:text-primaryDark transition-all duration-200 bg-primaryLight/10 rounded-xl hover:bg-primaryLight/30 border border-primaryLight/20 hover:border-primaryLight/50 form-element"
                                                    wire:navigate>
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button wire:click="delete({{ $kendaraan->id }})"
                                                    wire:confirm="Yakin ingin menghapus kendaraan ini? Semua unit terkait akan ikut terhapus."
                                                    class="group p-2 text-red-500 hover:text-red-700 transition-all duration-200 bg-red-50 rounded-xl hover:bg-red-100 border border-red-100 hover:border-red-200 form-element">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-8 py-16 text-center">
                                            <div
                                                class="mx-auto h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-textDark mb-2">Tidak ada data kendaraan</h3>
                                            <p class="text-sm text-textGray mb-6">Mulai dengan menambahkan master data
                                                kendaraan.</p>
                                            <a href="{{ route('admin.kendaraan.create') }}"
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors"
                                                wire:navigate>
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>Tambah Kendaraan Pertama</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($kendaraans->hasPages())
                        <div class="px-8 py-6 bg-gray-50 border-t border-inputBorder">
                            {{ $kendaraans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
