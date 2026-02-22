<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'livewire.pages.dashboard.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/settings.php';

// Admin Routes (User & Role Management)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Users Management
    Volt::route('/users', 'pages.admin.users.index')->name('users.index');
    Volt::route('/users/create', 'pages.admin.users.create')->name('users.create');
    Volt::route('/users/{user}/edit', 'pages.admin.users.edit')->name('users.edit');

    // Roles Management
    Volt::route('/roles', 'pages.admin.roles.index')->name('roles.index');
    Volt::route('/roles/create', 'pages.admin.roles.create')->name('roles.create');
    Volt::route('/roles/{role}/edit', 'pages.admin.roles.edit')->name('roles.edit');
});
