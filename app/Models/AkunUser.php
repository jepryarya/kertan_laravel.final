<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Illuminate\Support\Facades\Hash; // Tidak perlu di-import di sini jika hanya untuk casting

class AkunUser extends Authenticatable
{
    use Notifiable, HasApiTokens; // Combine the traits

    protected $table = 'akun_user'; // Specify the table name

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token', // Include both fields to hide
    ];

    // Disarankan untuk menambahkan ini agar password di-hash secara otomatis saat disimpan
    // dan untuk tipe data lainnya jika diperlukan
    protected $casts = [
        'password' => 'hashed', // Ini akan meng-hash password saat disimpan
        // 'email_verified_at' => 'datetime', // Tambahkan ini jika Anda punya kolom timestamp untuk verifikasi email
    ];

    /**
     * Get the warga record associated with the AkunUser.
     * Ini adalah relasi yang dibutuhkan agar $user->warga berfungsi.
     */
    public function warga()
    {
        // Parameter pertama: Model yang akan dihubungkan (Warga::class)
        // Parameter kedua (opsional): Foreign key di tabel 'warga' (defaultnya 'akun_user_id' jika nama fungsi 'warga' dan AkunUser)
        // Parameter ketiga (opsional): Local key di tabel 'akun_user' (defaultnya 'id')
        return $this->hasOne(Warga::class, 'akun_user_id', 'id');
    }
}