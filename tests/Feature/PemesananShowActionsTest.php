<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\KendaraanUnit;
use App\Models\Pelanggan;
use App\Models\Pemesanan;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PemesananShowActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->givePermissionTo('kelola_pemesanan');
        $this->actingAs($admin);
    }

    private function makePemesanan(string $status): Pemesanan
    {
        $kendaraan = Kendaraan::factory()->create();
        $unit = KendaraanUnit::factory()->create([
            'kendaraan_id' => $kendaraan->id,
            'status_unit' => $status === 'menunggu_konfirmasi' ? 'tersedia' : 'disewa',
        ]);
        $pelanggan = Pelanggan::factory()->create();

        return Pemesanan::factory()->create([
            'pelanggan_id' => $pelanggan->id,
            'kendaraan_unit_id' => $unit->id,
            'status_pemesanan' => $status,
            'waktu_mulai' => now()->subDays(2),
            'waktu_selesai' => now()->subDay(),
            'waktu_kembali' => null,
            'denda_per_hari' => 50000,
        ]);
    }

    public function test_approve_action_updates_status_to_disetujui(): void
    {
        $pemesanan = $this->makePemesanan('menunggu_konfirmasi');

        Livewire::test('admin.pemesanan.show', ['pemesanan' => $pemesanan->id])
            ->call('approve')
            ->assertHasNoErrors();

        $this->assertSame('disetujui', $pemesanan->fresh()->status_pemesanan);
        $this->assertSame('disewa', $pemesanan->kendaraanUnit->fresh()->status_unit);
    }

    public function test_reject_action_updates_status_to_ditolak(): void
    {
        $pemesanan = $this->makePemesanan('menunggu_konfirmasi');

        Livewire::test('admin.pemesanan.show', ['pemesanan' => $pemesanan->id])
            ->set('catatanAdmin', 'Stok tidak tersedia untuk unit ini.')
            ->call('reject')
            ->assertHasNoErrors();

        $this->assertSame('ditolak', $pemesanan->fresh()->status_pemesanan);
        $this->assertSame('tersedia', $pemesanan->kendaraanUnit->fresh()->status_unit);
    }

    public function test_complete_action_updates_status_and_calculates_denda(): void
    {
        $pemesanan = $this->makePemesanan('disetujui');

        Livewire::test('admin.pemesanan.show', ['pemesanan' => $pemesanan->id])
            ->set('waktuKembali', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('complete')
            ->assertHasNoErrors();

        $fresh = $pemesanan->fresh();
        $this->assertSame('selesai', $fresh->status_pemesanan);
        $this->assertGreaterThan(0, $fresh->denda);
        $this->assertSame('tersedia', $fresh->kendaraanUnit->fresh()->status_unit);
    }

    public function test_cancel_action_updates_status_to_dibatalkan(): void
    {
        $pemesanan = $this->makePemesanan('menunggu_konfirmasi');

        Livewire::test('admin.pemesanan.show', ['pemesanan' => $pemesanan->id])
            ->call('cancel')
            ->assertHasNoErrors();

        $this->assertSame('dibatalkan', $pemesanan->fresh()->status_pemesanan);
        $this->assertSame('tersedia', $pemesanan->kendaraanUnit->fresh()->status_unit);
    }
}
