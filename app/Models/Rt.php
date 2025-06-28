<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    protected $table = 'ketua_rt';

    protected $fillable = [
        'nama',
        'no_hp',
        'periode_mulai',
        'periode_selesai',
        'foto', 
    ];

    protected $casts = [
        'periode_mulai' => 'integer',
        'periode_selesai' => 'integer',
    ];
}
