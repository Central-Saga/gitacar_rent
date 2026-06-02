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
use Tests\TestCase;

class BackfillPemesananTierPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_dry_run_does_not_write_changes(): void
    {
        $pemesanan = $this->createPollutedPemesanan('mingguan', 7);

        $this->artisan('app:backfill-pemesanan-tier-pricing')->assertSuccessful();

        $fresh = $pemesanan->fresh();
        $this->assertSame('harian', $fresh->tipe_harga, 'dry-run should not change tipe_harga');
        $this->assertSame(2400000, (int) $fresh->harga_sewa, 'dry-run should not change harga_sewa');
        $this->assertSame(2400000, (int) $fresh->harga_per_hari, 'dry-run should not change harga_per_hari');
    }

    public function test_apply_repairs_weekly_polluted_booking(): void
    {
        $pemesanan = $this->createPollutedPemesanan('mingguan', 7);

        $this->artisan('app:backfill-pemesanan-tier-pricing', ['--apply' => true])->assertSuccessful();

        $fresh = $pemesanan->fresh();
        $this->assertSame('mingguan', $fresh->tipe_harga);
        $this->assertSame(2400000, (int) $fresh->harga_sewa);
        $this->assertSame(300000, (int) $fresh->harga_per_hari, 'harga_per_hari restored to actual daily rate');
    }

    public function test_apply_repairs_monthly_polluted_booking(): void
    {
        $kendaraan = $this->createKendaraan();
        $unit = $this->createUnit($kendaraan);
        $pelanggan = $this->createPelanggan();

        $start = Carbon::now()->subDays(60)->setHour(9)->setMinute(0);
        $end = $start->copy()->addMonth();

        $pemesanan = Pemesanan::create([
            'pelanggan_id' => $pelanggan->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'tipe_harga' => 'harian',
            'harga_sewa' => 9000000,
            'harga_per_hari' => 9000000,
            'total_harga' => 9000000,
            'denda_per_hari' => 300000,
            'status_pemesanan' => 'selesai',
        ]);

        $this->artisan('app:backfill-pemesanan-tier-pricing', ['--apply' => true])->assertSuccessful();

        $fresh = $pemesanan->fresh();
        $this->assertSame('bulanan', $fresh->tipe_harga);
        $this->assertSame(9000000, (int) $fresh->harga_sewa);
        $this->assertSame(300000, (int) $fresh->harga_per_hari);
    }

    public function test_apply_leaves_correct_booking_untouched(): void
    {
        $kendaraan = $this->createKendaraan();
        $unit = $this->createUnit($kendaraan);
        $pelanggan = $this->createPelanggan();

        $start = Carbon::now()->subDay()->setHour(9)->setMinute(0);
        $end = $start->copy()->addDays(3);

        $pemesanan = Pemesanan::create([
            'pelanggan_id' => $pelanggan->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'tipe_harga' => 'harian',
            'harga_sewa' => 300000,
            'harga_per_hari' => 300000,
            'total_harga' => 900000,
            'denda_per_hari' => 300000,
            'status_pemesanan' => 'menunggu_konfirmasi',
        ]);

        $this->artisan('app:backfill-pemesanan-tier-pricing', ['--apply' => true])->assertSuccessful();

        $fresh = $pemesanan->fresh();
        $this->assertSame('harian', $fresh->tipe_harga);
        $this->assertSame(300000, (int) $fresh->harga_sewa);
        $this->assertSame(300000, (int) $fresh->harga_per_hari);
        $this->assertSame(900000, (int) $fresh->total_harga);
    }

    public function test_apply_filters_by_id(): void
    {
        $pemesananA = $this->createPollutedPemesanan('mingguan', 7);
        $pemesananB = $this->createPollutedPemesanan('mingguan', 7);

        $this->artisan('app:backfill-pemesanan-tier-pricing', [
            '--apply' => true,
            '--id' => $pemesananA->id,
        ])->assertSuccessful();

        $this->assertSame(300000, (int) $pemesananA->fresh()->harga_per_hari, 'targeted id should be repaired');
        $this->assertSame(2400000, (int) $pemesananB->fresh()->harga_per_hari, 'untouched id should remain polluted');
    }

    private function createPollutedPemesanan(string $intendedTipe, int $durasiHari): Pemesanan
    {
        $kendaraan = $this->createKendaraan();
        $unit = $this->createUnit($kendaraan);
        $pelanggan = $this->createPelanggan();

        $start = Carbon::now()->subDays($durasiHari + 1)->setHour(9)->setMinute(0);
        $end = $start->copy()->addDays($durasiHari);

        $hargaSewaTier = $intendedTipe === 'mingguan'
            ? (int) $kendaraan->harga_sewa_per_minggu
            : (int) $kendaraan->harga_sewa_per_bulan;
        $jumlahUnit = $intendedTipe === 'mingguan'
            ? (int) ceil($durasiHari / 7)
            : (int) ceil($durasiHari / 30);
        $total = $hargaSewaTier * $jumlahUnit;

        return Pemesanan::create([
            'pelanggan_id' => $pelanggan->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'tipe_harga' => 'harian',
            'harga_sewa' => $hargaSewaTier,
            'harga_per_hari' => $hargaSewaTier,
            'total_harga' => $total,
            'denda_per_hari' => 300000,
            'status_pemesanan' => 'selesai',
        ]);
    }

    private function createKendaraan(): Kendaraan
    {
        return Kendaraan::create([
            'nama_kendaraan' => 'Test '.fake()->word(),
            'jenis_kendaraan' => 'mobil',
            'harga_sewa_per_hari' => 300000,
            'harga_sewa_per_minggu' => 2400000,
            'harga_sewa_per_bulan' => 9000000,
        ]);
    }

    private function createUnit(Kendaraan $kendaraan): KendaraanUnit
    {
        return KendaraanUnit::create([
            'kendaraan_id' => $kendaraan->id,
            'nomor_plat' => 'B '.fake()->unique()->numerify('#######'),
            'tahun' => '2024',
            'status_unit' => 'tersedia',
        ]);
    }

    private function createPelanggan(): Pelanggan
    {
        $user = User::factory()->create();
        $user->assignRole('pelanggan');

        return Pelanggan::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'email' => $user->email,
            'no_telp' => '08123456789',
            'alamat' => 'Test',
            'nik' => (string) fake()->numerify('################'),
        ]);
    }
}
