<?php

namespace Database\Factories;

use App\Models\Kendaraan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kendaraan>
 */
class KendaraanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kendaraan' => fake('id_ID')->words(2, true),
            'jenis_kendaraan' => fake()->randomElement(['motor', 'mobil']),
            'harga_sewa_per_hari' => fake()->numberBetween(100000, 1000000),
            'harga_sewa_per_minggu' => null,
            'harga_sewa_per_bulan' => null,
            'deskripsi' => fake('id_ID')->sentence(),
            'foto' => null,
        ];
    }
}
