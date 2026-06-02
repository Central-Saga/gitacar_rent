<?php

namespace Tests\Feature;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_manajemen_role_can_access_pemesanan(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $manajemen = Role::findByName('manajemen');

        $this->assertTrue(
            $manajemen->hasPermissionTo('kelola_pemesanan'),
            'Manajemen role should have kelola_pemesanan permission to access Pemesanan menu.'
        );
    }

    public function test_manajemen_role_keeps_cetak_laporan(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $manajemen = Role::findByName('manajemen');

        $this->assertTrue($manajemen->hasPermissionTo('mencetak_laporan'));
    }

    public function test_manajemen_role_keeps_kelola_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $manajemen = Role::findByName('manajemen');

        $this->assertTrue($manajemen->hasPermissionTo('kelola_user'));
    }

    public function test_admin_role_retains_all_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::findByName('admin');

        foreach (['kelola_user', 'kelola_pelanggan', 'kelola_kendaraan', 'kelola_pemesanan', 'kelola_diskon', 'mencetak_laporan'] as $perm) {
            $this->assertTrue($admin->hasPermissionTo($perm), "Admin should retain {$perm}");
        }
    }
}
