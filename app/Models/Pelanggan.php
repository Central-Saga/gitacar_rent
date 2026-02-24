<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

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
}
