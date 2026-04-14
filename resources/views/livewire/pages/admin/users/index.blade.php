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
use App\Models\User;
use Spatie\Permission\Models\Role;

layout('layouts.app');
title('User Management');

// Aktifkan pagination
usesPagination();

state([
    'search' => '',
]);

// Sinkronkan ke URL
state(['search'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

mount(function () {
    // Init state kalau perlu
});

// Lifecycle helpers Volt
updated([
    'search' => fn() => $this->resetPage(),
]);

with(function () {
    try {
        $query = User::query()->with('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistik global
        $globalStats = [
            'totalUsers' => User::count(),
            'withRoles' => User::has('roles')->count(),
            'withoutRoles' => User::doesntHave('roles')->count(),
            'rolesCount' => Role::count(),
        ];

        \Log::info('Users data loaded', [
            'users_count' => $users->count(),
            'total_users' => $globalStats['totalUsers'],
            'search' => $this->search,
        ]);

        return [
            'users' => $users,
            'stats' => $globalStats,
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading users data', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [
            'users' => collect(),
            'stats' => [
                'totalUsers' => 0,
                'withRoles' => 0,
                'withoutRoles' => 0,
                'rolesCount' => 0,
            ],
        ];
    }
});

?>

<div>
    <!-- Header -->
    <div class="mb-10">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Users</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Manajemen pengguna, role,
                    dan akses</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300"
                wire:navigate>
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat User Baru</span>
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-8">
        <input wire:model.live="search" type="text" placeholder="Cari user (nama / email)..."
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

    @if (session()->has('error'))
        <div
            class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-lg dark:from-red-900/20 dark:to-pink-900/20 dark:border-red-700/30 dark:text-red-200">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Users Table -->
    <div class="relative">
        <div class="relative bg-white shadow-sm rounded-2xl overflow-hidden border border-inputBorder">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-inputBorder">
                        <tr>
                            <th
                                class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                User</th>
                            <th
                                class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Roles</th>
                            <th
                                class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Dibuat</th>
                            <th
                                class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <div
                                                class="h-12 w-12 rounded-2xl bg-gray-50 text-gray-800 flex items-center justify-center border border-gray-200">
                                                <span
                                                    class="font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-5">
                                            <div class="text-sm font-bold text-textDark capitalize">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-xs text-textGray">ID: {{ $user->id
                                                                                                                }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-semibold text-textDark">
                                    {{ $user->email }}
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($user->roles->take(3) as $role)
                                                                    <span
                                                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">{{
                                            $role->name }}</span>
                                        @empty
                                            <span class="text-xs text-textGray">—</span>
                                        @endforelse
                                        @if($user->roles->count() > 3)
                                                                    <span
                                                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-primaryLight/20 text-primary border border-primaryLight/50">+{{
                                            $user->roles->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm text-textGray font-medium">
                                    {{ optional($user->created_at)->format('d M Y') }}
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="group p-2 text-primary hover:text-primaryDark transition-all duration-200 bg-primaryLight/10 rounded-xl hover:bg-primaryLight/30 border border-primaryLight/20 hover:border-primaryLight/50 form-element"
                                            wire:navigate>
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
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
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-textDark mb-2">Tidak ada user</h3>
                                    <p class="text-sm text-textGray mb-6">Mulai dengan menambahkan user baru.</p>
                                    <a href="{{ route('admin.users.create') }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors"
                                        wire:navigate>
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>Tambah User Pertama</span>
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-8 py-6 bg-gray-50 border-t border-inputBorder">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</div>

</div>