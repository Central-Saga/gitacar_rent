<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Pemesanan extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'pelanggan_id',
        'kendaraan_unit_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'harga_per_hari',
        'total_harga',
        'status_pemesanan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function kendaraanUnit()
    {
        return $this->belongsTo(KendaraanUnit::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('bukti_pembayaran')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }
}
