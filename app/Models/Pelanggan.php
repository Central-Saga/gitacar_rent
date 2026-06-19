<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'no_telp',
        'alamat',
        'nik',
        'foto_ktp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }
}
