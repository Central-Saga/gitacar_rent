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

class DashboardReminderColorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_reminder_penting_heading_uses_amber_not_red(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();

        $html = $response->getContent();

        $reminderPos = strpos($html, 'Reminder Penting');
        $this->assertNotFalse($reminderPos, 'Reminder Penting section should be rendered.');

        $headingSlice = substr($html, $reminderPos, 800);
        $this->assertStringContainsString('text-amber-600', $headingSlice);
        $this->assertDoesNotMatchRegularExpression('/text-red-/', $headingSlice);
    }

    public function test_kendaraan_terlambat_box_uses_amber_not_red(): void
    {
        $this->createOverduePemesanan();

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Kendaraan Terlambat', $html);

        $terlambatPos = strpos($html, 'Kendaraan Terlambat');
        $terlambatSlice = substr($html, $terlambatPos, 3000);

        $this->assertStringContainsString('bg-amber-50', $terlambatSlice);
        $this->assertStringContainsString('border-amber-500', $terlambatSlice);
        $this->assertDoesNotMatchRegularExpression('/bg-red-/', $terlambatSlice);
        $this->assertDoesNotMatchRegularExpression('/border-red-/', $terlambatSlice);
        $this->assertDoesNotMatchRegularExpression('/text-red-/', $terlambatSlice);
    }

    private function createOverduePemesanan(): Pemesanan
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
            'status_unit' => 'disewa',
        ]);

        return Pemesanan::create([
            'pelanggan_id' => $pelanggan->id,
            'kendaraan_unit_id' => $unit->id,
            'waktu_mulai' => Carbon::now()->subDays(5),
            'waktu_selesai' => Carbon::now()->subDay(),
            'tipe_harga' => 'harian',
            'harga_sewa' => 100000,
            'harga_per_hari' => 100000,
            'total_harga' => 500000,
            'status_pemesanan' => 'disetujui',
        ]);
    }
}
