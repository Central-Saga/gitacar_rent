<?php

use function Livewire\Volt\{layout, title, state, updated, with, usesPagination};
use App\Models\Pemesanan;

layout('layouts.app');
title('Kelola Pemesanan');

usesPagination();

state([
    'search' => '',
    'statusFilter' => '',
]);

state(['search', 'statusFilter'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

updated([
    'search' => fn() => $this->resetPage(),
    'statusFilter' => fn() => $this->resetPage(),
]);

with(function () {
    $query = Pemesanan::query()->with(['pelanggan', 'kendaraanUnit.kendaraan']);

    if ($this->search) {
        $query->whereHas('pelanggan', function ($q) {
            $q->where('nama', 'like', '%' . $this->search . '%')
              ->orWhere('email', 'like', '%' . $this->search . '%');
        })->orWhereHas('kendaraanUnit', function ($q) {
            $q->where('nomor_plat', 'like', '%' . $this->search . '%')
              ->orWhereHas('kendaraan', function ($q2) {
                  $q2->where('nama_kendaraan', 'like', '%' . $this->search . '%');
              });
        });
    }

    if ($this->statusFilter) {
        $query->where('status_pemesanan', $this->statusFilter);
    }

    $pemesanans = $query->orderBy('created_at', 'desc')->paginate(10);

    return [
        'pemesanans' => $pemesanans,
    ];
});
?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Pemesanan</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Manajemen data pemesanan atau reservasi unit kendaraan</p>
                    </div>
                    <a href="{{ route('admin.pemesanan.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300"
                        wire:navigate>
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Pemesanan Baru</span>
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-8 flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input wire:model.live="search" type="text" placeholder="Cari pemesan atau plat nomor..."
                        class="block w-full px-4 py-3 h-[46px] rounded-2xl bg-white text-textDark placeholder-textGray border border-inputBorder focus:ring-2 focus:ring-primary focus:outline-none text-sm font-medium">
                </div>
                <div class="w-full md:w-48 relative">
                    <select wire:model.live="statusFilter" class="block w-full pl-4 pr-10 h-[46px] appearance-none rounded-2xl bg-white text-textDark border border-inputBorder focus:ring-2 focus:ring-primary focus:outline-none text-sm font-medium cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-textGray">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="relative bg-white shadow-sm rounded-2xl overflow-hidden border border-inputBorder">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 border-b border-inputBorder">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pemesan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu Sewa</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total Harga</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($pemesanans as $p)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-textDark">{{ $p->pelanggan->nama ?? 'Unknown' }}</div>
                                        <div class="text-xs text-textGray">{{ $p->pelanggan->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-textDark">{{ $p->kendaraanUnit->kendaraan->nama_kendaraan ?? 'Unknown' }}</div>
                                        <div class="text-xs text-textGray bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded inline-block mt-1 font-mono">
                                            {{ $p->kendaraanUnit->nomor_plat ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-textDark">
                                            {{ $p->waktu_mulai->format('d M Y H:i') }}
                                        </div>
                                        <div class="text-sm font-medium text-textDark">
                                            s/d {{ $p->waktu_selesai->format('d M Y H:i') }}
                                        </div>
                                        @php $durHrs = $p->waktu_mulai->diffInHours($p->waktu_selesai); $durDays = max(1, (int) ceil($durHrs / 24)); @endphp
                                        <div class="text-xs text-textGray mt-1">{{ $durDays }} Hari</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-textDark">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</div>
                                        <div class="text-xs text-textGray mt-1 mb-1">Rp {{ number_format($p->harga_per_hari, 0, ',', '.') }}/hari</div>
                                        @if($p->promo_id)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                Promo: {{ $p->promo->kode_promo }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $badges = [
                                                'menunggu_konfirmasi' => 'bg-yellow-100 text-yellow-800',
                                                'disetujui' => 'bg-blue-100 text-blue-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                'dibatalkan' => 'bg-gray-100 text-gray-800',
                                                'selesai' => 'bg-green-100 text-green-800',
                                            ];
                                            $labels = [
                                                'menunggu_konfirmasi' => 'Menunggu',
                                                'disetujui' => 'Disetujui',
                                                'ditolak' => 'Ditolak',
                                                'dibatalkan' => 'Batal',
                                                'selesai' => 'Selesai',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badges[$p->status_pemesanan] ?? 'bg-gray-100' }}">
                                            {{ $labels[$p->status_pemesanan] ?? strtoupper($p->status_pemesanan) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.pemesanan.show', $p->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-primaryLight/10 text-primary hover:bg-primaryLight/20 hover:text-primaryDark rounded-xl transition-colors font-semibold"
                                            wire:navigate>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pemesanan</h3>
                                        <p class="mt-1 text-sm text-gray-500">Belum ada pemesanan yang masuk atau sesuai kriteria pencarian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($pemesanans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $pemesanans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
