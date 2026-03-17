<?php

namespace App\Console\Commands;

use App\Models\Pelanggan;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;

class CleanupNonAdminData extends Command
{
    protected $signature = 'app:cleanup-non-admin-data';

    protected $description = 'Bersihkan data booking, pembayaran, dan user non-admin sambil merapikan nama admin';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $deletedPemesananCount = Pemesanan::query()->count();

        Pemesanan::query()
            ->get()
            ->each(function (Pemesanan $pemesanan): void {
                $pemesanan->delete();
            });

        $nonAdminUsers = User::query()
            ->whereDoesntHave('roles', function (Builder $query): void {
                $query->where('name', 'admin');
            })
            ->get();

        $nonAdminUserIds = $nonAdminUsers->modelKeys();

        $deletedPelangganCount = Pelanggan::query()
            ->whereIn('user_id', $nonAdminUserIds)
            ->delete();

        $deletedUserCount = 0;

        $nonAdminUsers->each(function (User $user) use (&$deletedUserCount): void {
            $user->syncRoles([]);
            $user->delete();
            $deletedUserCount++;
        });

        $renamedAdminCount = User::query()
            ->whereHas('roles', function (Builder $query): void {
                $query->where('name', 'admin');
            })
            ->get()
            ->each(function (User $user): void {
                $user->forceFill([
                    'name' => 'admin',
                ])->save();
            })
            ->count();

        $this->components->info('Pembersihan data selesai.');
        $this->line("Pemesanan dihapus: {$deletedPemesananCount}");
        $this->line("Pelanggan non-admin dihapus: {$deletedPelangganCount}");
        $this->line("User non-admin dihapus: {$deletedUserCount}");
        $this->line("Admin dirapikan: {$renamedAdminCount}");

        return self::SUCCESS;
    }
}
