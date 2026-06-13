<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pelanggan>
 */
class PelangganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama' => fake('id_ID')->name(),
            'email' => fake('id_ID')->unique()->safeEmail(),
            'no_telp' => fake('id_ID')->phoneNumber(),
            'alamat' => fake('id_ID')->address(),
            'nik' => fake('id_ID')->nik(),
            'foto_ktp' => null,
        ];
    }
}
