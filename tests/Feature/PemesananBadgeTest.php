<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\KendaraanUnit;
use App\Models\Pelanggan;
use App\Models\Pemesanan;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PemesananBadgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_badge_counts_active_pemesanan_only(): void
    {
        $this->createPemesanan('menunggu_konfirmasi');
        $this->createPemesanan('disetujui');
        $this->createPemesanan('selesai');
        $this->createPemesanan('ditolak');
        $this->createPemesanan('dibatalkan');

        Livewire::test('components.pemesanan-badge')
            ->assertSet('count', 2)
            ->assertSee('2');
    }

    public function test_badge_shows_zero_when_only_inactive_pemesanan_exist(): void
    {
        $this->createPemesanan('selesai');
        $this->createPemesanan('dibatalkan');
        $this->createPemesanan('ditolak');

        Livewire::test('components.pemesanan-badge')
            ->assertSet('count', 0)
            ->assertSee('0');
    }

    public function test_badge_caps_display_at_99_plus(): void
    {
        for ($i = 0; $i < 101; $i++) {
            $this->createPemesanan('menunggu_konfirmasi');
        }

        Livewire::test('components.pemesanan-badge')
            ->assertSet('count', 101)
            ->assertSee('99+');
    }

    private function createPemesanan(string $status): Pemesanan
    {
        $user = User::factory()->create();
        $user->assignRole('pelanggan');

        $pelanggan = Pelanggan::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'email' => $user->email,
            'no_telp' => '08123456789',
            'alamat' => 'Test',
            'nik' => (string) fake()->numerify('################'),
        ]);

        $kendaraan = Kendaraan::create([
            'nama_kendaraan' => 'Test Car',
            'jenis_kendaraan' => 'mobil',
            'harga_sewa_per_hari' => 100000,
        ]);

        $unit = KendaraanUnit::create([
            'kendaraan_id' => $kendaraan->id,
            'nomor_plat' => 'B '.fake()->unique()->numerify('#######'),
            'tahun' => '2024',
            'status_unit' => 'tersedia',
        ]);

        return Pemesanan::create([
            'pelanggan_id' => $pelanggan->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => Carbon::now(),
            'waktu_selesai' => Carbon::now()->addDay(),
            'harga_per_hari' => 100000,
            'total_harga' => 100000,
            'status_pemesanan' => $status,
        ]);
    }
}
