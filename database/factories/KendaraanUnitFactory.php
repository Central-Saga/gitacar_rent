<?php

namespace Database\Factories;

use App\Models\Kendaraan;
use App\Models\KendaraanUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KendaraanUnit>
 */
class KendaraanUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kendaraan_id' => Kendaraan::factory(),
            'nomor_plat' => fake()->regexify('[A-Z]{1,2} [0-9]{4} [A-Z]{2,3}'),
            'tahun' => fake()->numberBetween(2018, 2026),
            'status_unit' => fake()->randomElement(['tersedia', 'dibooking', 'disewa', 'maintenance', 'nonaktif']),
        ];
    }
}
