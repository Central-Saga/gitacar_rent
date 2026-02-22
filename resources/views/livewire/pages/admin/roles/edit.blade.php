<?php

use function Livewire\Volt\{
    layout,
    title,
    state,
    mount
};
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

layout('layouts.app');
title('Edit Role');

state([
    'role' => null,
    'name' => '',
    'guard_name' => 'web',
    'selectedPermissions' => [],
]);

mount(function (Role $role) {
    $this->role = $role;
    $this->name = $role->name;
    $this->guard_name = $role->guard_name;
    $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    $this->permissions = Permission::orderBy('name')->get();
});

$save = function () {
    $this->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . $this->role->id,
        'guard_name' => 'required|string|max:255',
    ]);

    $this->role->update([
        'name' => $this->name,
        'guard_name' => $this->guard_name,
    ]);

    $this->role->syncPermissions($this->selectedPermissions);

    session()->flash('message', 'Role berhasil diperbarui!');
    return $this->redirect(route('admin.roles.index'));
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-textDark">Edit Role</h1>
                    <p class="mt-1 text-sm text-textGray">Edit role "{{ $role->name }}" untuk sistem Watugangga
                        Riverside Guest House</p>
                </div>
                <a href="{{ route('admin.roles.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300"
                    wire:navigate>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-inputBorder overflow-hidden">
                <form wire:submit="save" class="p-8 space-y-8">
                    <!-- Basic Information -->
                    <div x-data="{ open: true }"
                        class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-left focus:outline-none">
                            <h3 class="text-lg font-bold text-textDark flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                                    <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Informasi Dasar
                            </h3>
                            <svg class="w-5 h-5 text-textGray transform transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-bold text-textDark mb-3">
                                        Nama Role <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="name" type="text" id="name"
                                        class="block w-full px-4 py-3 border border-inputBorder rounded-xl bg-white text-textDark placeholder-textGray focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm"
                                        placeholder="Contoh: Admin, Staff, Customer">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="guard_name" class="block text-sm font-bold text-textDark mb-3">
                                        Guard Name <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select wire:model="guard_name" id="guard_name"
                                            class="block w-full px-4 py-3 pr-10 border border-inputBorder rounded-xl bg-white text-textDark focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none text-sm appearance-none">
                                            <option value="web">Web</option>
                                            <option value="api">API</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                            <svg class="h-4 w-4 text-textGray" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('guard_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Role Info -->
                    <div x-data="{ open: false }"
                        class="bg-primaryLight/10 border border-primaryLight/20 rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-left focus:outline-none">
                            <h4 class="text-sm font-bold text-primaryDark">Informasi Role</h4>
                            <svg class="w-5 h-5 text-primaryDark transform transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2" class="mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-primary">
                                <div class="bg-white rounded-xl p-3 border border-primaryLight/20 shadow-sm">
                                    <p class="font-semibold text-primaryDark mb-1">Dibuat</p>
                                    <p>{{ $role->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-primaryLight/20 shadow-sm">
                                    <p class="font-semibold text-primaryDark mb-1">Diperbarui</p>
                                    <p>{{ $role->updated_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="bg-white rounded-xl p-3 border border-primaryLight/20 shadow-sm">
                                    <p class="font-semibold text-primaryDark mb-1">Permissions</p>
                                    <p>{{ $role->permissions->count() }} permission</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div x-data="{ open: false }"
                        class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-left focus:outline-none">
                            <div>
                                <h3 class="text-lg font-bold text-textDark flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-primary/20 rounded-xl flex items-center justify-center border border-primary/30">
                                        <svg class="w-4 h-4 text-primaryDark" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                            </path>
                                        </svg>
                                    </div>
                                    Permissions
                                </h3>
                                <p class="mt-1 text-sm text-textGray">Pilih permission yang akan diberikan kepada role
                                    ini</p>
                            </div>
                            <svg class="w-5 h-5 text-textGray transform transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                            <div class="bg-white rounded-2xl p-6 border border-inputBorder">
                                <div class="flex flex-col space-y-3">
                                    @foreach($this->permissions as $permission)
                                        <label
                                            class="group flex items-center p-4 bg-white rounded-xl border border-inputBorder hover:border-primaryLight hover:bg-primaryLight/10 cursor-pointer transition-all duration-200">
                                            <input wire:model="selectedPermissions" type="checkbox"
                                                value="{{ $permission->name }}"
                                                class="h-5 w-5 text-primary focus:ring-primary border-inputBorder rounded-lg">
                                            <span
                                                class="ml-3 text-sm font-medium text-textDark group-hover:text-primary transition-colors">
                                                {{ $permission->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                @if($this->permissions->count() === 0)
                                    <div class="text-center py-12">
                                        <div
                                            class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-textDark mb-2">Tidak ada permission</h3>
                                        <p class="text-sm text-textGray">Tidak ada permission yang tersedia saat ini</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-inputBorder">
                        <a href="{{ route('admin.roles.index') }}"
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-2.5 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors">
                            Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>