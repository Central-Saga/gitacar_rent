<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use App\Models\Kendaraan;
use App\Models\KendaraanUnit;
use App\Models\Pemesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PemesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Pelanggan Dummy (dan Usernya)
        $user1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budisantoso@example.com',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole('pelanggan');

        $pelanggan1 = Pelanggan::create([
            'user_id' => $user1->id,
            'nama' => 'Budi Santoso',
            'email' => 'budisantoso@example.com',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'nik' => '1234567890123456'
        ]);

        $user2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'sitiaminah@example.com',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole('pelanggan');

        $pelanggan2 = Pelanggan::create([
            'user_id' => $user2->id,
            'nama' => 'Siti Aminah',
            'email' => 'sitiaminah@example.com',
            'no_telp' => '089876543210',
            'alamat' => 'Jl. Sudirman No. 10, Bandung',
            'nik' => '6543210987654321'
        ]);

        // 2. Buat Kendaraan Dummy
        $kendaraanA = Kendaraan::create([
            'nama_kendaraan' => 'Toyota Avanza',
            'jenis_kendaraan' => 'mobil',
            'harga_sewa_per_hari' => 300000,
            'deskripsi' => 'Mobil keluarga nyaman 7 penumpang.'
        ]);

        $kendaraanB = Kendaraan::create([
            'nama_kendaraan' => 'Honda Vario 160',
            'jenis_kendaraan' => 'motor',
            'harga_sewa_per_hari' => 100000,
            'deskripsi' => 'Motor matic gesit dan irit.'
        ]);

        // 3. Buat Unit Kendaraan
        $unit1 = KendaraanUnit::create([
            'kendaraan_id' => $kendaraanA->id,
            'nomor_plat' => 'B 1234 ABC',
            'tahun' => '2022',
            'status_unit' => 'tersedia'
        ]);

        $unit2 = KendaraanUnit::create([
            'kendaraan_id' => $kendaraanB->id,
            'nomor_plat' => 'D 5678 DEF',
            'tahun' => '2023',
            'status_unit' => 'tersedia'
        ]);

        $unit3 = KendaraanUnit::create([
            'kendaraan_id' => $kendaraanA->id,
            'nomor_plat' => 'B 9999 XYZ',
            'tahun' => '2021',
            'status_unit' => 'tersedia'
        ]);

        // 4. Buat Pemesanan Skenario

        // Skenario 1: Selesai TEPAT WAKTU (Tanpa Denda)
        $waktuMulai1 = Carbon::now()->subDays(3)->setHour(9)->setMinute(0); // 3 Hari lalu jam 09:00
        $waktuSelesai1 = $waktuMulai1->copy()->addDays(2); // Durasi 2 Hari (Selesai 1 Hari lalu jam 09:00)
        $waktuKembali1 = $waktuSelesai1->copy()->subMinutes(30); // Kembali jam 08:30 (Tepat waktu)

        Pemesanan::create([
            'pelanggan_id' => $pelanggan1->id,
            'kendaraan_unit_id' => $unit1->id,
            'waktu_mulai' => $waktuMulai1,
            'waktu_selesai' => $waktuSelesai1,
            'waktu_kembali' => $waktuKembali1,
            'harga_per_hari' => 300000,
            'total_harga' => 600000, // 2 hari = 600.000
            'denda_per_hari' => 300000,
            'hari_terlambat' => 0,
            'denda' => 0,
            'status_pemesanan' => 'selesai',
            'catatan' => 'Dikembalikan tepat waktu, kondisi aman.'
        ]);
        // Unit 1 tetap 'tersedia' karena sudah selesai

        // Skenario 2: Selesai TERLAMBAT (Kena Denda)
        $waktuMulai2 = Carbon::now()->subDays(5)->setHour(10)->setMinute(0); // 5 Hari lalu jam 10:00
        $waktuSelesai2 = $waktuMulai2->copy()->addDays(1); // Durasi 1 Hari (Selesai 4 Hari lalu jam 10:00)

        // Sengaja terlambat 27 jam -> (27 jam = 1 hari 3 jam -> ceil(27/24) = 2 Hari Terlambat)
        $waktuKembali2 = $waktuSelesai2->copy()->addHours(27);

        Pemesanan::create([
            'pelanggan_id' => $pelanggan2->id,
            'kendaraan_unit_id' => $unit2->id,
            'waktu_mulai' => $waktuMulai2,
            'waktu_selesai' => $waktuSelesai2,
            'waktu_kembali' => $waktuKembali2,
            'harga_per_hari' => 100000,
            'total_harga' => 100000, // 1 hari
            'denda_per_hari' => 100000,
            'hari_terlambat' => 2, // Telat 27 jam -> hitung 2 hari
            'denda' => 200000, // 2 hari x 100.000
            'status_pemesanan' => 'selesai',
            'catatan' => 'Terlambat lebih dari sehari.'
        ]);
        // Unit 2 tetap 'tersedia' karena sudah selesai

        // Skenario 3: Sedang disewa (Belum dikembalikan)
        $waktuMulai3 = Carbon::now()->subDays(1)->setHour(14)->setMinute(0); // Kemarin jam 14:00
        $waktuSelesai3 = $waktuMulai3->copy()->addDays(3); // Sewa 3 hari

        Pemesanan::create([
            'pelanggan_id' => $pelanggan1->id,
            'kendaraan_unit_id' => $unit3->id,
            'waktu_mulai' => $waktuMulai3,
            'waktu_selesai' => $waktuSelesai3,
            'waktu_kembali' => null,
            'harga_per_hari' => 300000,
            'total_harga' => 900000, // 3 hari
            'denda_per_hari' => 300000,
            'hari_terlambat' => 0,
            'denda' => 0,
            'status_pemesanan' => 'disetujui',
            'catatan' => 'Sedang dipakai jalan-jalan.'
        ]);

        // Ubah status unit 3 jadi disewa
        $unit3->update(['status_unit' => 'disewa']);
    }
}
