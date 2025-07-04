<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satpam extends Model
{
    protected $table = 'satpam';

    protected $fillable = [
        'nama',
        'no_hp',
        'shift',
        'akun_user_id',
        'foto_satpam', // Tambahkan baris ini
    ];

    // Relasi ke AkunUser (optional tapi direkomendasikan)
    public function akunUser()
    {
        return $this->belongsTo(AkunUser::class, 'akun_user_id');
    }
}