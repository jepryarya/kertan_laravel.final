<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key dari tabel

    protected $fillable = [
        'nama',
        'nik',
        'kk',
        'alamat_rumah',
        'no_rumah',
        'no_hp',
        'akun_user_id',
        'foto_rumah', // Kolom untuk menyimpan path/URL foto rumah
        'jumlah_anggota_keluarga', // Kolom untuk menyimpan jumlah anggota keluarga
    ];

    // Relasi ke AkunUser (Warga memiliki satu AkunUser)
    public function akunUser()
    {
        return $this->belongsTo(AkunUser::class, 'akun_user_id', 'id');
    }

    // Relasi balikan dari PengajuanSurat (jika Warga ingin melihat daftar pengajuan mereka)
    public function pengajuanSurat()
    {
        return $this->hasMany(PengajuanSurat::class, 'warga_id', 'id');
    }
}
