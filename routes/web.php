<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'landingpage.home.index')->name('home');
Volt::route('/contact-us', 'landingpage.contact us.index')->name('contact');
Volt::route('/about-us', 'landingpage.aboutus.index')->name('about');

// Katalog Routes
Volt::route('/katalog/mobil', 'landingpage.katalog.mobil.index')->name('katalog.mobil');
Volt::route('/katalog/motor', 'landingpage.katalog.motor.index')->name('katalog.motor');
Volt::route('/katalog/{kendaraan}', 'landingpage.katalog.detail')->name('katalog.detail');

// Booking User
Volt::route('/booking/{kendaraanUnit?}', 'landingpage.booking.index')->middleware(['auth', 'verified'])->name('booking');

Volt::route('/reservasi', 'landingpage.reservasi.index')->middleware(['auth', 'verified'])->name('reservasi');

Volt::route('dashboard', 'dashboard.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';

// Admin Routes (User & Role Management)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Users Management
    Route::middleware('can:kelola_user')->group(function () {
        Volt::route('/users', 'admin.users.index')->name('users.index');
        Volt::route('/users/create', 'admin.users.create')->name('users.create');
        Volt::route('/users/{user}/edit', 'admin.users.edit')->name('users.edit');

        // Roles Management
        Volt::route('/roles', 'admin.roles.index')->name('roles.index');
        Volt::route('/roles/create', 'admin.roles.create')->name('roles.create');
        Volt::route('/roles/{role}/edit', 'admin.roles.edit')->name('roles.edit');
    });

    // Pelanggans Management
    Route::middleware('can:kelola_pelanggan')->group(function () {
        Volt::route('/pelanggan', 'admin.pelanggan.index')->name('pelanggans.index');
        Volt::route('/pelanggan/create', 'admin.pelanggan.create')->name('pelanggans.create');
        Volt::route('/pelanggan/{pelanggan}/edit', 'admin.pelanggan.edit')->name('pelanggans.edit');
    });

    // Kendaraan Management
    Route::middleware('can:kelola_kendaraan')->group(function () {
        Volt::route('/kendaraan', 'admin.kendaraan.index')->name('kendaraan.index');
        Volt::route('/kendaraan/create', 'admin.kendaraan.create')->name('kendaraan.create');
        Volt::route('/kendaraan/{kendaraan}/edit', 'admin.kendaraan.edit')->name('kendaraan.edit');

        // Kendaraan Unit routes
        Volt::route('kendaraan-units', 'admin.kendaraan-unit.index')->name('kendaraan-units.index');
        Volt::route('kendaraan-units/create', 'admin.kendaraan-unit.create')->name('kendaraan-units.create');
        Volt::route('kendaraan-units/{kendaraanUnit}/edit', 'admin.kendaraan-unit.edit')->name('kendaraan-units.edit');
    });

    // Pemesanan routes
    Route::middleware('can:kelola_pemesanan')->group(function () {
        Volt::route('pemesanan', 'admin.pemesanan.index')->name('pemesanan.index');
        Volt::route('pemesanan/create', 'admin.pemesanan.create')->name('pemesanan.create');
        Volt::route('pemesanan/{pemesanan}', 'admin.pemesanan.show')->name('pemesanan.show');
    });

    // Promo & Diskon routes
    Route::middleware('can:kelola_diskon')->group(function () {
        Volt::route('promo', 'admin.promo.index')->name('promo.index');
        Volt::route('promo/create', 'admin.promo.create')->name('promo.create');
        Volt::route('promo/{promo}/edit', 'admin.promo.edit')->name('promo.edit');
    });
});
