<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    /** @use HasFactory<\Database\Factories\KendaraanFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_kendaraan',
        'jenis_kendaraan',
        'harga_sewa_per_hari',
        'deskripsi',
        'foto',
    ];

    public function units()
    {
        return $this->hasMany(KendaraanUnit::class);
    }
}
