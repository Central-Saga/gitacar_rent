<?php

use App\Models\Kendaraan;
use App\Models\KendaraanUnit;
use App\Models\Pelanggan;
use App\Models\Pemesanan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('cleans all booking data and removes non admin users', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
    ]);
    $admin->assignRole('admin');

    $pelangganUser = User::factory()->create([
        'name' => 'Customer User',
    ]);
    $pelangganUser->assignRole('pelanggan');

    $pelanggan = Pelanggan::factory()->create([
        'user_id' => $pelangganUser->id,
    ]);

    $kendaraan = Kendaraan::query()->create([
        'nama_kendaraan' => 'Toyota Avanza',
        'jenis_kendaraan' => 'mobil',
        'harga_sewa_per_hari' => 350000,
        'deskripsi' => 'Unit test kendaraan',
    ]);

    $unit = KendaraanUnit::query()->create([
        'kendaraan_id' => $kendaraan->id,
        'nomor_plat' => 'DK 1234 TEST',
        'tahun' => '2024',
        'status_unit' => 'tersedia',
    ]);

    Pemesanan::query()->create([
        'pelanggan_id' => $pelanggan->id,
        'kendaraan_unit_id' => $unit->id,
        'waktu_mulai' => now(),
        'waktu_selesai' => now()->addDay(),
        'harga_per_hari' => 350000,
        'total_harga' => 350000,
        'denda_per_hari' => 350000,
        'hari_terlambat' => 0,
        'denda' => 0,
        'status_pemesanan' => 'menunggu_konfirmasi',
    ]);

    $this->artisan('app:cleanup-non-admin-data')
        ->expectsOutputToContain('Pembersihan data selesai.')
        ->assertExitCode(0);

    expect(User::query()->count())->toBe(1);
    expect(Pelanggan::query()->count())->toBe(0);
    expect(Pemesanan::query()->count())->toBe(0);

    $admin->refresh();

    expect($admin->name)->toBe('admin');
    $this->assertDatabaseMissing('users', ['id' => $pelangganUser->id]);
});

it('seeds only one admin user named admin without demo bookings', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->count())->toBe(1);
    expect(Pelanggan::query()->count())->toBe(0);
    expect(Pemesanan::query()->count())->toBe(0);

    $admin = User::query()->first();

    expect($admin)->not->toBeNull();
    expect($admin->name)->toBe('admin');
    expect($admin->email)->toBe('admin@example.com');
    expect($admin->hasRole('admin'))->toBeTrue();
});
