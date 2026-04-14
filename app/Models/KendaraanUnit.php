<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KendaraanUnit extends Model
{
    /** @use HasFactory<\Database\Factories\KendaraanUnitFactory> */
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'nomor_plat',
        'tahun',
        'status_unit',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }
}
