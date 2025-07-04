<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tamu extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model ini
    protected $table = 'tamu_pendatang';

    // Kolom yang bisa diisi secara massal (mass assignable)
    protected $fillable = [
        'nama',
        'no_identitas',
        'foto_ktp',
        'alamat_asal',
        'ke_rumah',
        'alasan_kunjungan',
        'waktu_masuk',
        'waktu_keluar', // Ini bisa diisi nanti saat tamu keluar
        'status',
    ];

    // Kolom yang harus di-cast ke tipe data tertentu (opsional, tapi bagus untuk datetime)
    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];
}
