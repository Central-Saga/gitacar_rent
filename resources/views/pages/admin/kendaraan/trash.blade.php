<?php

use function Livewire\Volt\{layout, title, state, with};
use App\Models\Kendaraan;
use App\Models\KendaraanUnit;

layout('layouts.app');
title('Arsip Kendaraan');

state(['tab' => 'kendaraan']);

$restoreKendaraan = function ($id) {
    $kendaraan = Kendaraan::onlyTrashed()->findOrFail($id);
    $kendaraan->restore();
    session()->flash('message', 'Kendaraan berhasil dipulihkan!');
};

$restoreUnit = function ($id) {
    $unit = KendaraanUnit::onlyTrashed()->findOrFail($id);
    $unit->restore();
    session()->flash('message', 'Unit kendaraan berhasil dipulihkan!');
};

with(function () {
    return [
        'trashedKendaraans' => Kendaraan::onlyTrashed()->orderBy('deleted_at', 'desc')->get(),
        'trashedUnits' => KendaraanUnit::onlyTrashed()->with('kendaraan')->orderBy('deleted_at', 'desc')->get(),
    ];
});

?>

<div>
    <div class="py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Arsip Kendaraan</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Data kendaraan dan unit yang telah dihapus</p>
                </div>
                <a href="{{ route('admin.kendaraan.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition-all duration-300"
                    wire:navigate>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        @if(session('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium">
                {{ session('message') }}
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="flex gap-1 mb-8 bg-gray-100 p-1 rounded-xl w-fit">
            <button wire:click="$set('tab', 'kendaraan')"
                class="px-5 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 {{ $tab === 'kendaraan' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Kendaraan ({{ $trashedKendaraans->count() }})
            </button>
            <button wire:click="$set('tab', 'unit')"
                class="px-5 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 {{ $tab === 'unit' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Unit Kendaraan ({{ $trashedUnits->count() }})
            </button>
        </div>

        @if($tab === 'kendaraan')
            <div class="bg-white rounded-2xl shadow-sm border border-inputBorder overflow-hidden">
                @forelse($trashedKendaraans as $k)
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-textDark">{{ $k->nama_kendaraan }}</h4>
                                <p class="text-xs text-textGray mt-0.5">{{ ucfirst($k->jenis_kendaraan) }} &middot; Dihapus {{ $k->deleted_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <button wire:click="restoreKendaraan({{ $k->id }})"
                            class="px-4 py-2 bg-green-50 hover:bg-green-100 text-green-600 hover:text-green-700 font-bold text-xs rounded-xl border border-green-200 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Pulihkan
                        </button>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="mt-4 text-sm font-medium text-gray-500">Tidak ada kendaraan yang dihapus.</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-inputBorder overflow-hidden">
                @forelse($trashedUnits as $u)
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-textDark">{{ $u->nomor_plat }}</h4>
                                <p class="text-xs text-textGray mt-0.5">{{ $u->kendaraan->nama_kendaraan ?? 'Unknown' }} &middot; Dihapus {{ $u->deleted_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <button wire:click="restoreUnit({{ $u->id }})"
                            class="px-4 py-2 bg-green-50 hover:bg-green-100 text-green-600 hover:text-green-700 font-bold text-xs rounded-xl border border-green-200 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Pulihkan
                        </button>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="mt-4 text-sm font-medium text-gray-500">Tidak ada unit kendaraan yang dihapus.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
