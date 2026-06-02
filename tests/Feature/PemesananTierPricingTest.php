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

class PemesananTierPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_weekly_booking_saves_tipe_harga_mingguan_with_correct_pricing(): void
    {
        $kendaraan = $this->createKendaraan([
            'harga_sewa_per_hari' => 300000,
            'harga_sewa_per_minggu' => 2400000,
            'harga_sewa_per_bulan' => 9000000,
        ]);
        $unit = $this->createUnit($kendaraan);

        $start = Carbon::now()->addDay()->setHour(9)->setMinute(0);
        $end = $start->copy()->addWeek();

        $pemesanan = Pemesanan::create([
            'pelanggan_id' => $this->createPelanggan()->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'tipe_harga' => 'mingguan',
            'harga_sewa' => $kendaraan->harga_sewa_per_minggu,
            'harga_per_hari' => $kendaraan->harga_sewa_per_hari,
            'total_harga' => $kendaraan->harga_sewa_per_minggu,
            'denda_per_hari' => $kendaraan->harga_sewa_per_hari,
            'status_pemesanan' => 'menunggu_konfirmasi',
        ]);

        $fresh = $pemesanan->fresh();

        $this->assertSame('mingguan', $fresh->tipe_harga);
        $this->assertSame(2400000, (int) $fresh->harga_sewa);
        $this->assertSame(300000, (int) $fresh->harga_per_hari, 'harga_per_hari must stay the actual daily rate, not the weekly tier price');
        $this->assertSame(2400000, (int) $fresh->total_harga);
    }

    public function test_monthly_booking_saves_tipe_harga_bulanan(): void
    {
        $kendaraan = $this->createKendaraan([
            'harga_sewa_per_hari' => 300000,
            'harga_sewa_per_minggu' => 2400000,
            'harga_sewa_per_bulan' => 9000000,
        ]);
        $unit = $this->createUnit($kendaraan);

        $start = Carbon::now()->addDay()->setHour(9)->setMinute(0);
        $end = $start->copy()->addMonth();

        $pemesanan = Pemesanan::create([
            'pelanggan_id' => $this->createPelanggan()->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'tipe_harga' => 'bulanan',
            'harga_sewa' => $kendaraan->harga_sewa_per_bulan,
            'harga_per_hari' => $kendaraan->harga_sewa_per_hari,
            'total_harga' => $kendaraan->harga_sewa_per_bulan,
            'denda_per_hari' => $kendaraan->harga_sewa_per_hari,
            'status_pemesanan' => 'menunggu_konfirmasi',
        ]);

        $fresh = $pemesanan->fresh();

        $this->assertSame('bulanan', $fresh->tipe_harga);
        $this->assertSame(9000000, (int) $fresh->harga_sewa);
        $this->assertSame(300000, (int) $fresh->harga_per_hari);
    }

    public function test_daily_booking_falls_back_to_harian(): void
    {
        $kendaraan = $this->createKendaraan([
            'harga_sewa_per_hari' => 300000,
            'harga_sewa_per_minggu' => null,
            'harga_sewa_per_bulan' => null,
        ]);
        $unit = $this->createUnit($kendaraan);

        $start = Carbon::now()->addDay()->setHour(9)->setMinute(0);
        $end = $start->copy()->addDays(3);

        $pemesanan = Pemesanan::create([
            'pelanggan_id' => $this->createPelanggan()->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'harga_sewa' => $kendaraan->harga_sewa_per_hari,
            'harga_per_hari' => $kendaraan->harga_sewa_per_hari,
            'total_harga' => $kendaraan->harga_sewa_per_hari * 3,
            'denda_per_hari' => $kendaraan->harga_sewa_per_hari,
            'status_pemesanan' => 'menunggu_konfirmasi',
        ]);

        $fresh = $pemesanan->fresh();

        $this->assertSame('harian', $fresh->tipe_harga);
        $this->assertSame(300000, (int) $fresh->harga_sewa);
        $this->assertSame(300000, (int) $fresh->harga_per_hari);
    }

    public function test_show_page_renders_weekly_label_for_weekly_booking(): void
    {
        $kendaraan = $this->createKendaraan([
            'harga_sewa_per_hari' => 300000,
            'harga_sewa_per_minggu' => 2400000,
            'harga_sewa_per_bulan' => 9000000,
        ]);
        $unit = $this->createUnit($kendaraan);

        $start = Carbon::now()->subDay()->setHour(9)->setMinute(0);
        $end = $start->copy()->addWeek();

        $pemesanan = Pemesanan::create([
            'pelanggan_id' => $this->createPelanggan()->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'tipe_harga' => 'mingguan',
            'harga_sewa' => 2400000,
            'harga_per_hari' => 300000,
            'total_harga' => 2400000,
            'denda_per_hari' => 300000,
            'status_pemesanan' => 'menunggu_konfirmasi',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $response = $this->get(route('admin.pemesanan.show', $pemesanan->id));
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Harga per Minggu', $html);
        $this->assertStringContainsString('2.400.000', $html);
        $this->assertStringContainsString('1 Minggu', $html);
        $this->assertStringNotContainsString('Harga per Hari', $html);
    }

    public function test_30_day_booking_is_bulanan_even_when_kendaraan_has_no_harga_sewa_per_bulan(): void
    {
        $kendaraan = $this->createKendaraan([
            'harga_sewa_per_hari' => 500000,
            'harga_sewa_per_minggu' => null,
            'harga_sewa_per_bulan' => null,
        ]);
        $unit = $this->createUnit($kendaraan);

        $start = Carbon::now()->addDay()->setHour(9)->setMinute(0);
        $end = $start->copy()->addDays(30);

        $pemesanan = Pemesanan::create([
            'pelanggan_id' => $this->createPelanggan()->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $end,
            'tipe_harga' => 'bulanan',
            'harga_sewa' => 14000000,
            'harga_per_hari' => 500000,
            'total_harga' => 14000000,
            'denda_per_hari' => 500000,
            'status_pemesanan' => 'menunggu_konfirmasi',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $response = $this->get(route('admin.pemesanan.show', $pemesanan->id));
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Harga per Bulan', $html);
        $this->assertStringContainsString('14.000.000', $html);
        $this->assertStringContainsString('1 Bulan', $html);
        $this->assertStringNotContainsString('Harga per Hari', $html);
    }

    private function createKendaraan(array $overrides = []): Kendaraan
    {
        return Kendaraan::create(array_merge([
            'nama_kendaraan' => 'Test '.fake()->word(),
            'jenis_kendaraan' => 'mobil',
            'harga_sewa_per_hari' => 300000,
        ], $overrides));
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
