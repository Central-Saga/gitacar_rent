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
use App\Models\Pelanggan;
use App\Models\User;

layout('layouts.app');
title('Kelola Pelanggan');

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
    $query = Pelanggan::query()->with('user');

    if ($this->search) {
        $query->where('nama', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('nik', 'like', '%' . $this->search . '%');
    }

    $pelanggans = $query->orderBy('created_at', 'desc')->paginate(10);

    return [
        'pelanggans' => $pelanggans,
    ];
});

$delete = function (Pelanggan $pelanggan) {
    if ($pelanggan->user) {
        $pelanggan->user->delete(); // This will cascade delete the pelanggan because of the foreign key constraint
    } else {
        $pelanggan->delete();
    }
    session()->flash('message', 'Pelanggan berhasil dihapus!');
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Pelanggan</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Manajemen data pelanggan
                        </p>
                    </div>
                    <a href="{{ route('admin.pelanggans.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300"
                        wire:navigate>
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Pelanggan</span>
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-8">
                <input wire:model.live="search" type="text" placeholder="Cari pelanggan (nama / email / nik)..."
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
                                        Pelanggan</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Kontak</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        NIK</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($pelanggans as $pelanggan)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    @if($pelanggan->foto_ktp)
                                                        <img class="h-12 w-12 rounded-2xl object-cover border border-gray-200"
                                                            src="{{ Storage::url($pelanggan->foto_ktp) }}"
                                                            alt="{{ $pelanggan->nama }}">
                                                    @else
                                                        <div
                                                            class="h-12 w-12 rounded-2xl bg-primaryLight/30 text-primaryDark flex items-center justify-center border border-primaryLight/50">
                                                            <span
                                                                class="text-white font-bold text-lg">{{ strtoupper(substr($pelanggan->nama ?? 'P', 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-5">
                                                    <div class="text-sm font-bold text-textDark capitalize">
                                                        {{ $pelanggan->nama ?: '-' }}
                                                    </div>
                                                    <div class="text-xs text-textGray">ID: {{ $pelanggan->user_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-sm text-textDark">
                                            <div class="font-semibold">{{ $pelanggan->email ?: '-' }}</div>
                                            <div class="text-textGray mt-1">{{ $pelanggan->no_telp ?: '-' }}</div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-sm font-medium text-textDark">
                                            {{ $pelanggan->nik ?: '-' }}
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('admin.pelanggans.edit', $pelanggan) }}"
                                                    class="group p-2 text-primary hover:text-primaryDark transition-all duration-200 bg-primaryLight/10 rounded-xl hover:bg-primaryLight/30 border border-primaryLight/20 hover:border-primaryLight/50 form-element"
                                                    wire:navigate>
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button wire:click="delete({{ $pelanggan->id }})"
                                                    wire:confirm="Yakin ingin menghapus pelanggan ini beserta akun usernya?"
                                                    class="group p-2 text-red-500 hover:text-red-700 transition-all duration-200 bg-red-50 rounded-xl hover:bg-red-100 border border-red-200 hover:border-red-300 form-element">
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
                                        <td colspan="4" class="px-8 py-16 text-center">
                                            <div
                                                class="mx-auto h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-textDark mb-2">Tidak ada pelanggan</h3>
                                            <p class="text-sm text-textGray mb-6">Mulai dengan menambahkan pelanggan baru.
                                            </p>
                                            <a href="{{ route('admin.pelanggans.create') }}"
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors"
                                                wire:navigate>
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>Tambah Pelanggan</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($pelanggans->hasPages())
                        <div class="px-8 py-6 bg-gray-50 border-t border-inputBorder">
                            {{ $pelanggans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>