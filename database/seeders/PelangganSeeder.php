<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if user already exists
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'pelanggan@gita.com'],
            [
                'name' => 'Demo Pelanggan',
                'password' => bcrypt('password'),
            ]
        );
        $user->assignRole('pelanggan');

        \App\Models\Pelanggan::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => $user->name,
                'email' => $user->email,
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Bypass Ngurah Rai',
                'nik' => '5171000000000001',
            ]
        );
    }
}
