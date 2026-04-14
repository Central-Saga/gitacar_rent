<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.landingpage.home.index')->name('home');
Volt::route('/contact-us', 'pages.landingpage.contact us.index')->name('contact');
Volt::route('/about-us', 'pages.landingpage.aboutus.index')->name('about');

// Katalog Routes
Volt::route('/katalog/mobil', 'pages.landingpage.katalog.mobil.index')->name('katalog.mobil');
Volt::route('/katalog/motor', 'pages.landingpage.katalog.motor.index')->name('katalog.motor');
Volt::route('/katalog/{kendaraan}', 'pages.landingpage.katalog.detail')->name('katalog.detail');

// Booking User
Volt::route('/booking/{kendaraanUnit?}', 'pages.landingpage.booking.index')->middleware(['auth', 'verified'])->name('booking');

Volt::route('/reservasi', 'pages.landingpage.reservasi.index')->middleware(['auth', 'verified'])->name('reservasi');

Volt::route('dashboard', 'pages.dashboard.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/settings.php';

// Admin Routes (User & Role Management)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Users Management
    Route::middleware('can:kelola_user')->group(function () {
        Volt::route('/users', 'pages.admin.users.index')->name('users.index');
        Volt::route('/users/create', 'pages.admin.users.create')->name('users.create');
        Volt::route('/users/{user}/edit', 'pages.admin.users.edit')->name('users.edit');

        // Roles Management
        Volt::route('/roles', 'pages.admin.roles.index')->name('roles.index');
        Volt::route('/roles/create', 'pages.admin.roles.create')->name('roles.create');
        Volt::route('/roles/{role}/edit', 'pages.admin.roles.edit')->name('roles.edit');
    });

    // Pelanggans Management
    Route::middleware('can:kelola_pelanggan')->group(function () {
        Volt::route('/pelanggan', 'pages.admin.pelanggan.index')->name('pelanggans.index');
        Volt::route('/pelanggan/create', 'pages.admin.pelanggan.create')->name('pelanggans.create');
        Volt::route('/pelanggan/{pelanggan}/edit', 'pages.admin.pelanggan.edit')->name('pelanggans.edit');
    });

    // Kendaraan Management
    Route::middleware('can:kelola_kendaraan')->group(function () {
        Volt::route('/kendaraan', 'pages.admin.kendaraan.index')->name('kendaraan.index');
        Volt::route('/kendaraan/create', 'pages.admin.kendaraan.create')->name('kendaraan.create');
        Volt::route('/kendaraan/{kendaraan}/edit', 'pages.admin.kendaraan.edit')->name('kendaraan.edit');

        // Kendaraan Unit routes
        Volt::route('kendaraan-units', 'pages.admin.kendaraan-unit.index')->name('kendaraan-units.index');
        Volt::route('kendaraan-units/create', 'pages.admin.kendaraan-unit.create')->name('kendaraan-units.create');
        Volt::route('kendaraan-units/{kendaraanUnit}/edit', 'pages.admin.kendaraan-unit.edit')->name('kendaraan-units.edit');
    });

    // Pemesanan routes
    Route::middleware('can:kelola_pemesanan')->group(function () {
        Volt::route('pemesanan', 'pages.admin.pemesanan.index')->name('pemesanan.index');
        Volt::route('pemesanan/create', 'pages.admin.pemesanan.create')->name('pemesanan.create');
        Volt::route('pemesanan/{pemesanan}', 'pages.admin.pemesanan.show')->name('pemesanan.show');
    });

    // Promo & Diskon routes
    Route::middleware('can:kelola_diskon')->group(function () {
        Volt::route('promo', 'pages.admin.promo.index')->name('promo.index');
        Volt::route('promo/create', 'pages.admin.promo.create')->name('promo.create');
        Volt::route('promo/{promo}/edit', 'pages.admin.promo.edit')->name('promo.edit');
    });
});
