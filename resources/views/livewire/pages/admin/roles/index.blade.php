<?php

use function Livewire\Volt\{
    layout,
    title,
    state,
    mount,
    with,
    updated,
    usesPagination,
    url
};
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

layout('layouts.app');
title('Role Management');

// aktifkan pagination
usesPagination();

state([
    'search' => '',
    'sortField' => 'created_at',
    'sortDirection' => 'desc',
]);

// sinkronkan ke URL
state(['search'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

mount(function () {
    // Initialize component
});

// gunakan helper lifecycle Volt
updated([
    'search' => fn() => $this->resetPage(),
]);

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
    $this->resetPage();
};

$toggleStatus = function ($roleId) {
    try {
        $role = Role::findOrFail($roleId);
        if (in_array($role->name, ['Admin', 'Owner', 'Pengunjung'])) {
            session()->flash('error', 'Status role default tidak dapat diubah.');
            return;
        }
        $role->is_active = !$role->is_active;
        $role->save();
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal memperbarui status: ' . $e->getMessage());
    }
};

with(function () {
    try {
        $query = Role::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $roles = $query->orderBy($this->sortField ?: 'created_at', $this->sortDirection ?: 'desc')
            ->paginate(10);

        // Count users for each role
        foreach ($roles as $role) {
            $role->usersCount = $role->users()->count();
        }

        // Global statistics
        $globalStats = [
            'totalRoles' => Role::count(),
            'adminRole' => Role::where('name', 'Admin')->count(),
            'ownerRole' => Role::where('name', 'Owner')->count(),
            'customerRole' => Role::where('name', 'Pengunjung')->count(),
            'customRoles' => Role::whereNotIn('name', ['Admin', 'Owner', 'Pengunjung'])->count(),
        ];

        // Debug logging
        \Log::info('Roles data loaded successfully', [
            'roles_count' => $roles->count(),
            'total_roles' => $globalStats['totalRoles'],
            'search' => $this->search,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection
        ]);

        return [
            'roles' => $roles,
            'permissions' => Permission::all(),
            'stats' => $globalStats
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading roles data', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'roles' => collect(),
            'permissions' => collect(),
            'stats' => [
                'totalRoles' => 0,
                'adminRole' => 0,
                'ownerRole' => 0,
                'customerRole' => 0,
                'customRoles' => 0,
            ]
        ];
    }
});

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-bold text-textDark">Kelola Role</h1>
                        <p class="mt-2 text-sm text-textGray font-medium">Sistem manajemen role & permission yang
                            canggih</p>
                    </div>
                    <a href="{{ route('admin.roles.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300"
                        wire:navigate>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span>Buat Role Baru</span>
                    </a>
                </div>
            </div>



            <!-- Search & Sort Section -->
            <div class="mb-8">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                    <div class="flex-grow w-full">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-6 w-6 text-textGray" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input wire:model.live="search" type="text" placeholder="Cari role berdasarkan nama..."
                                class="block w-full pl-14 pr-4 py-3 border border-inputBorder rounded-2xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:outline-none text-sm font-medium">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="sortBy('name')"
                            class="px-6 py-3 border border-inputBorder rounded-2xl focus:outline-none text-textDark bg-white hover:bg-gray-50 transition-colors duration-200 font-medium text-sm">
                            <span class="flex items-center gap-2">
                                Name
                                @if($sortField === 'name')
                                    <span class="text-xs text-primary">
                                        @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </span>
                        </button>
                        <button wire:click="sortBy('created_at')"
                            class="px-6 py-3 border border-inputBorder rounded-2xl focus:outline-none text-textDark bg-white hover:bg-gray-50 transition-colors duration-200 font-medium text-sm">
                            <span class="flex items-center gap-2">
                                Created
                                @if($sortField === 'created_at')
                                    <span class="text-xs text-primary">
                                        @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session()->has('message'))
                <div
                    class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 shadow-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if (session()->has('error'))
                <div
                    class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 shadow-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Roles Table -->
            <div class="relative">
                <div class="relative bg-white shadow-sm rounded-2xl overflow-hidden border border-inputBorder">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 border-b border-inputBorder">
                                <tr>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Users</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Permissions</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Dibuat</th>
                                    <th
                                        class="px-8 py-6 text-right text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($roles as $role)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    @if($role->name === 'Admin')
                                                        <div
                                                            class="h-12 w-12 rounded-2xl bg-accentCoral/20 flex items-center justify-center border border-accentCoral/30">
                                                            <span
                                                                class="text-accentCoral font-bold text-lg">{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                                                        </div>
                                                    @elseif($role->name === 'Owner')
                                                        <div
                                                            class="h-12 w-12 rounded-2xl bg-accentYellow/20 flex items-center justify-center border border-accentYellow/30">
                                                            <span
                                                                class="text-accentYellow font-bold text-lg">{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                                                        </div>
                                                    @elseif($role->name === 'Pengunjung')
                                                        <div
                                                            class="h-12 w-12 rounded-2xl bg-primary/20 flex items-center justify-center border border-primary/30">
                                                            <span
                                                                class="text-primaryDark font-bold text-lg">{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="h-12 w-12 rounded-2xl bg-backgroundSoft flex items-center justify-center border border-inputBorder">
                                                            <span
                                                                class="text-textDark font-bold text-lg">{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-5">
                                                    <div class="text-sm font-bold text-textDark capitalize">
                                                        {{ $role->name }}
                                                    </div>
                                                    @if(in_array($role->name, ['Admin', 'Owner', 'Pengunjung']))
                                                        <span
                                                            class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-accentYellow/20 text-accentYellow border border-accentYellow/30">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            System Role
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                                    <span
                                                        class="text-primaryDark font-bold text-sm">{{ $role->usersCount }}</span>
                                                </div>
                                                <span
                                                    class="ml-3 text-sm font-semibold text-textDark">{{ $role->usersCount }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($role->permissions->take(3) as $permission)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                        {{ $permission->name }}
                                                    </span>
                                                @endforeach
                                                @if($role->permissions->count() > 3)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-primaryLight/20 text-primary border border-primaryLight/50">
                                                        +{{ $role->permissions->count() - 3 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-sm text-textGray font-medium">
                                            {{ $role->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-4">
                                                <!-- Status Switch -->
                                                @if(!in_array($role->name, ['Admin', 'Owner', 'Pengunjung']))
                                                    <button wire:click="toggleStatus({{ $role->id }})"
                                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 {{ $role->is_active ? 'bg-primary' : 'bg-red-500' }}"
                                                        role="switch" aria-checked="{{ $role->is_active ? 'true' : 'false' }}">
                                                        <span class="sr-only">Toggle status</span>
                                                        <span
                                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $role->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                                    </button>
                                                @else
                                                    <!-- System roles are always active and cannot be toggled -->
                                                    <div
                                                        class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent bg-primary/50 opacity-50 cursor-not-allowed">
                                                        <span
                                                            class="pointer-events-none inline-block h-5 w-5 transform translate-x-5 rounded-full bg-white shadow ring-0"></span>
                                                    </div>
                                                @endif

                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.roles.edit', $role) }}"
                                                    class="group p-2 text-primary hover:text-primaryDark transition-all duration-200 bg-primaryLight/10 rounded-xl hover:bg-primaryLight/30 border border-primaryLight/20 hover:border-primaryLight/50">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
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
                                                <svg class="h-12 w-12 text-textGray" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-textDark mb-2">Tidak ada role</h3>
                                            <p class="text-sm text-textGray mb-6">Mulai dengan menambahkan role baru untuk
                                                sistem Anda.</p>
                                            <a href="{{ route('admin.roles.create') }}"
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors"
                                                wire:navigate>
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                <span>Tambah Role Pertama</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($roles->hasPages())
                        <div class="px-8 py-6 bg-gray-50 border-t border-inputBorder">
                            {{ $roles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>