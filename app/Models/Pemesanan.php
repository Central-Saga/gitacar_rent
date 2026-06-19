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
        'promo_id',
        'waktu_mulai',
        'waktu_selesai',
        'waktu_kembali',
        'tipe_harga',
        'harga_sewa',
        'harga_per_hari',
        'total_diskon',
        'total_harga',
        'denda_per_hari',
        'hari_terlambat',
        'denda',
        'status_pemesanan',
        'catatan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'waktu_kembali' => 'datetime',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function kendaraanUnit()
    {
        return $this->belongsTo(KendaraanUnit::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('bukti_pembayaran')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }
}
