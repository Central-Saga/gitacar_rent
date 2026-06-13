<?php

namespace Database\Factories;

use App\Models\KendaraanUnit;
use App\Models\Pelanggan;
use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemesanan>
 */
class PemesananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pelanggan_id' => Pelanggan::factory(),
            'kendaraan_unit_id' => KendaraanUnit::factory(),
            'waktu_mulai' => now()->subDays(2),
            'waktu_selesai' => now()->addDay(),
            'waktu_kembali' => null,
            'tipe_harga' => 'harian',
            'harga_sewa' => 100000,
            'harga_per_hari' => 100000,
            'total_diskon' => 0,
            'total_harga' => 300000,
            'denda_per_hari' => 50000,
            'hari_terlambat' => 0,
            'denda' => 0,
            'status_pemesanan' => 'selesai',
            'catatan' => null,
        ];
    }
}
