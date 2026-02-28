<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_promo',
        'deskripsi',
        'diskon_persen',
        'maksimal_diskon',
        'kuota_total',
        'kuota_terpakai',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->startOfDay();
        if ($now->lt($this->tanggal_mulai) || $now->gt($this->tanggal_selesai)) {
            return false;
        }

        if ($this->kuota_total !== null && $this->kuota_terpakai >= $this->kuota_total) {
            return false;
        }

        return true;
    }
}
