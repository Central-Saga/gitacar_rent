<?php

use function Livewire\Volt\{
    layout,
    title,
    state,
    updated,
    with,
    usesPagination,
    on
};
use App\Models\KendaraanUnit;

layout('layouts.app');
title('Kelola Unit Kendaraan');

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
        $query = KendaraanUnit::query()->with('kendaraan');

        if ($this->search) {
            $query->where('nomor_plat', 'like', '%' . $this->search . '%')
                ->orWhereHas('kendaraan', function ($q) {
                    $q->where('nama_kendaraan', 'like', '%' . $this->search . '%');
                });
        }

        $units = $query->orderBy('created_at', 'desc')->paginate(10);

        return [
            'units' => $units,
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading kendaraan unit data', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [
            'units' => collect(),
        ];
    }
});

on([
    'delete-unit' => function ($id) {
        $unit = KendaraanUnit::find($id);
        if ($unit) {
            $unit->delete();
            $this->dispatch('swal:toast', title: 'Unit Kendaraan berhasil dihapus!', icon: 'success');
            $this->resetPage();
        }
    }
]);

$updateStatus = function (KendaraanUnit $unit, $status) {
    if (in_array($status, ['tersedia', 'disewa', 'maintenance', 'nonaktif'])) {
        $unit->update(['status_unit' => $status]);
        $this->dispatch('swal:toast', title: 'Status unit kendaraan berhasil diperbarui!', icon: 'success');
    }
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Unit Kendaraan</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Manajemen inventaris plat
                            nomor dan status unit</p>
                    </div>
                    <a href="{{ route('admin.kendaraan-units.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300"
                        wire:navigate>
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Unit Kendaraan</span>
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-8">
                <input wire:model.live="search" type="text" placeholder="Cari unit (Plat / Nama Kendaraan)..."
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
                                        Unit (Plat Nomor)</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Kendaraan</th>
                                    <th
                                        class="px-8 py-6 text-center text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Tahun</th>
                                    <th
                                        class="px-8 py-6 text-center text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-8 py-6 text-right text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($units as $unit)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-10 w-auto px-4 py-2 bg-yellow-100 text-yellow-800 font-mono font-bold tracking-widest uppercase rounded-lg border-2 border-yellow-400/50 shadow-sm flex items-center justify-center">
                                                    {{ $unit->nomor_plat }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                                                @if($unit->kendaraan->foto)
                                                    <img src="{{ Storage::url($unit->kendaraan->foto) }}"
                                                        alt="{{ $unit->kendaraan->nama_kendaraan }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-textDark">{{ $unit->kendaraan->nama_kendaraan }}
                                                </div>
                                                <div class="text-xs text-textGray capitalize">
                                                    {{ $unit->kendaraan->jenis_kendaraan }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-center text-sm text-textGray font-bold">
                                            {{ $unit->tahun }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $statusColors = [
                                                    'tersedia' => 'bg-green-50 text-green-700 border-green-200 focus:border-green-500 focus:ring-green-500',
                                                    'disewa' => 'bg-blue-50 text-blue-700 border-blue-200 focus:border-blue-500 focus:ring-blue-500',
                                                    'maintenance' => 'bg-orange-50 text-orange-700 border-orange-200 focus:border-orange-500 focus:ring-orange-500',
                                                    'nonaktif' => 'bg-gray-50 text-gray-700 border-gray-200 focus:border-gray-500 focus:ring-gray-500',
                                                ];
                                                $colorClass = $statusColors[$unit->status_unit] ?? 'bg-gray-50 text-gray-700 border-gray-200 focus:border-gray-500 focus:ring-gray-500';
                                            @endphp
                                            <div class="inline-block relative w-36">
                                                <select wire:change="updateStatus({{ $unit->id }}, $event.target.value)"
                                                    class="block w-full appearance-none rounded-full border text-xs font-bold uppercase tracking-wider py-1.5 pl-4 pr-8 focus:ring-2 focus:outline-none transition-colors cursor-pointer {{ $colorClass }}">
                                                    <option class="text-gray-900 bg-white" value="tersedia" {{ $unit->status_unit === 'tersedia' ? 'selected' : '' }}>Tersedia
                                                    </option>
                                                    <option class="text-gray-900 bg-white" value="disewa" {{ $unit->status_unit === 'disewa' ? 'selected' : '' }}>Sedang Disewa
                                                    </option>
                                                    <option class="text-gray-900 bg-white" value="maintenance" {{ $unit->status_unit === 'maintenance' ? 'selected' : '' }}>Maintenance
                                                    </option>
                                                    <option class="text-gray-900 bg-white" value="nonaktif" {{ $unit->status_unit === 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                                    </option>
                                                </select>
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-current opacity-70">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-3">
                                                <a href="{{ route('admin.kendaraan-units.edit', $unit) }}"
                                                    class="group p-2 text-primary hover:text-primaryDark transition-all duration-200 bg-primaryLight/10 rounded-xl hover:bg-primaryLight/30 border border-primaryLight/20 hover:border-primaryLight/50 form-element"
                                                    wire:navigate>
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button wire:click="$dispatch('swal:confirm', {
                                                                title: 'Hapus Unit Kendaraan?',
                                                                text: 'Yakin ingin menghapus unit kendaraan {{ $unit->nomor_plat }}?',
                                                                icon: 'warning',
                                                                method: 'delete-unit',
                                                                id: {{ $unit->id }}
                                                            })"
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
                                        <td colspan="5" class="px-8 py-16 text-center">
                                            <div
                                                class="mx-auto h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-textDark mb-2">Tidak ada data unit kendaraan
                                            </h3>
                                            <p class="text-sm text-textGray mb-6">Mulai dengan mendaftarkan plat nomor unit
                                                kendaraan baru.</p>
                                            <a href="{{ route('admin.kendaraan-units.create') }}"
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors"
                                                wire:navigate>
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>Tambah Unit Pertama</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($units->hasPages())
                        <div class="px-8 py-6 bg-gray-50 border-t border-inputBorder">
                            {{ $units->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>