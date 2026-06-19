<?php

namespace App\Models;

use Database\Factories\KendaraanUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KendaraanUnit extends Model
{
    /** @use HasFactory<KendaraanUnitFactory> */
    use HasFactory, SoftDeletes;

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
