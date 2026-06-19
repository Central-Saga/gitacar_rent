<?php

namespace App\Console\Commands;

use App\Models\Kendaraan;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillPemesananTierPricing extends Command
{
    protected $signature = 'app:backfill-pemesanan-tier-pricing
        {--apply : Actually write changes (default is dry-run)}
        {--id= : Limit to a single pemesanan id}';

    protected $description = 'Repair historical pemesanan rows polluted by the old booking flow: infer tipe_harga + harga_sewa from duration and total_harga, restore harga_per_hari from the Kendaraan snapshot.';

    public function handle(): int
    {
        $query = Pemesanan::query()
            ->with(['kendaraanUnit.kendaraan'])
            ->orderBy('id');

        if ($id = $this->option('id')) {
            $query->where('id', (int) $id);
        }

        $rows = $query->get();
        $apply = (bool) $this->option('apply');
        $candidates = [];

        foreach ($rows as $pemesanan) {
            $repaired = $this->reconcile($pemesanan);
            if ($repaired === null) {
                continue;
            }
            $candidates[] = $repaired;
        }

        if ($candidates === []) {
            $this->info('Tidak ada pemesanan yang perlu diperbaiki.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Tipe lama', 'Tipe baru', 'Harga sewa lama', 'Harga sewa baru', 'Harga/hari lama', 'Harga/hari baru', 'Total harga'],
            array_map(fn (array $r) => [
                $r['id'],
                $r['old_tipe'],
                $r['new_tipe'],
                number_format($r['old_harga_sewa'], 0, ',', '.'),
                number_format($r['new_harga_sewa'], 0, ',', '.'),
                number_format($r['old_harga_per_hari'], 0, ',', '.'),
                number_format($r['new_harga_per_hari'], 0, ',', '.'),
                number_format($r['total_harga'], 0, ',', '.'),
            ], $candidates)
        );

        $count = count($candidates);
        $this->line('');
        $this->line("Ditemukan {$count} pemesanan yang perlu diperbaiki.");

        if (! $apply) {
            $this->warn('Mode DRY-RUN. Jalankan dengan --apply untuk menulis perubahan.');

            return self::SUCCESS;
        }

        foreach ($candidates as $row) {
            Pemesanan::query()
                ->where('id', $row['id'])
                ->update([
                    'tipe_harga' => $row['new_tipe'],
                    'harga_sewa' => $row['new_harga_sewa'],
                    'harga_per_hari' => $row['new_harga_per_hari'],
                ]);
        }

        $this->info("Berhasil memperbaiki {$count} pemesanan.");

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>|null null = no change needed
     */
    private function reconcile(Pemesanan $pemesanan): ?array
    {
        $kendaraan = $pemesanan->kendaraanUnit?->kendaraan;
        if (! $kendaraan instanceof Kendaraan) {
            return null;
        }

        $actualDailyRate = (int) $kendaraan->harga_sewa_per_hari;
        $oldTipe = $pemesanan->tipe_harga ?? 'harian';
        $oldHargaSewa = (int) $pemesanan->harga_sewa;
        $oldHargaPerHari = (int) $pemesanan->harga_per_hari;

        $start = Carbon::parse($pemesanan->waktu_mulai);
        $end = Carbon::parse($pemesanan->waktu_selesai);
        $diffHours = $start->diffInHours($end);
        $durasi = max(1, (int) ceil($diffHours / 24));
        $total = (int) $pemesanan->total_harga;

        $newTipe = 'harian';
        $newHargaSewa = $actualDailyRate;
        $newHargaPerHari = $actualDailyRate;

        if ($durasi >= 30 && $kendaraan->harga_sewa_per_bulan) {
            $newTipe = 'bulanan';
            $newHargaSewa = (int) $kendaraan->harga_sewa_per_bulan;
        } elseif ($durasi >= 7 && $kendaraan->harga_sewa_per_minggu) {
            $newTipe = 'mingguan';
            $newHargaSewa = (int) $kendaraan->harga_sewa_per_minggu;
        }

        $newHargaPerHari = $actualDailyRate;

        if ($oldTipe === $newTipe && $oldHargaSewa === $newHargaSewa && $oldHargaPerHari === $newHargaPerHari) {
            return null;
        }

        return [
            'id' => $pemesanan->id,
            'old_tipe' => $oldTipe,
            'new_tipe' => $newTipe,
            'old_harga_sewa' => $oldHargaSewa,
            'new_harga_sewa' => $newHargaSewa,
            'old_harga_per_hari' => $oldHargaPerHari,
            'new_harga_per_hari' => $newHargaPerHari,
            'total_harga' => $total,
        ];
    }
}
