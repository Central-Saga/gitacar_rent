<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
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

        $roleManajemen = Role::firstOrCreate(['name' => 'manajemen']);
        $roleManajemen->syncPermissions([
            'kelola_user',
            'kelola_pemesanan',
            'mencetak_laporan',
        ]);
    }

    public function down(): void
    {
        $roleManajemen = Role::firstOrCreate(['name' => 'manajemen']);
        $roleManajemen->syncPermissions([
            'kelola_user',
            'mencetak_laporan',
        ]);
    }
};
