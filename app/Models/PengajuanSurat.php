<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Penting untuk URL storage

class PengajuanSurat extends Model
{
    protected $table = 'pengajuan_surat';
    protected $primaryKey = 'id';
    protected $fillable = ['warga_id', 'jenis_surat', 'keterangan', 'status', 'foto_surat_path', 'keperluan_surat', 'tanggal_diperlukan'];

    // Tambahkan atribut ini agar secara otomatis disertakan saat model diubah ke array/JSON
    protected $appends = ['attachment_url'];

    // Accessor untuk mendapatkan URL lengkap dari foto_surat_path
    public function getAttachmentUrlAttribute()
    {
        if ($this->foto_surat_path) {
            // Pastikan 'public' adalah disk yang sama dengan yang Anda gunakan untuk menyimpan
            // dan Anda sudah menjalankan 'php artisan storage:link'
            return Storage::disk('public')->url($this->foto_surat_path);
        }
        return null; // Mengembalikan null jika tidak ada foto
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id', 'id');
    }
}