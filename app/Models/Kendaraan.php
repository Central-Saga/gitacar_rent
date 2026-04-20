<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Kendaraan extends Model
{
    /** @use HasFactory<\Database\Factories\KendaraanFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_kendaraan',
        'jenis_kendaraan',
        'harga_sewa_per_hari',
        'harga_sewa_per_minggu',
        'harga_sewa_per_bulan',
        'deskripsi',
        'foto',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(KendaraanUnit::class);
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! is_string($this->foto) || $this->foto === '') {
            return null;
        }

        return $this->resolveFotoPath($this->foto);
    }

    public function getPlaceholderFotoUrlAttribute(): string
    {
        return Storage::disk('public')->url('img/hero_section_home.png');
    }

    protected function resolveFotoPath(string $path): string
    {
        $normalizedPath = ltrim($path, '/');

        if (Str::startsWith($normalizedPath, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($normalizedPath, 'storage/')) {
            return asset($normalizedPath);
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            return Storage::disk('public')->url($normalizedPath);
        }

        return asset($normalizedPath);
    }
}
