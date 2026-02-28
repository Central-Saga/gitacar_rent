<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'kelola_user',
            'kelola_pelanggan',
            'kelola_kendaraan',
            'kelola_pemesanan',
            'kelola_diskon',
            'mencetak_laporan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // A) Admin
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all()); // Admin gets all permissions mentioned

        // B) Pelanggan
        $rolePelanggan = Role::firstOrCreate(['name' => 'pelanggan']);
        $rolePelanggan->givePermissionTo([
            'kelola_pemesanan',
        ]);

        // C) Manajemen
        $roleManajemen = Role::firstOrCreate(['name' => 'manajemen']);
        $roleManajemen->givePermissionTo([
            'kelola_user',
            'mencetak_laporan',
        ]);
    }
}

