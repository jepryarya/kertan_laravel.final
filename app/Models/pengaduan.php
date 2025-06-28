<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Penting untuk URL storage
// Hapus baris ini: use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaduan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengaduan'; // Nama tabel di database adalah 'pengaduan'

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warga_id', 
        'kategori',
        'isi_pengaduan',
        'foto_laporan_path',
        'status',
        'tanggapan_admin', // Tambahkan ini jika tanggapan admin juga diisi massal
    ];

    /**
     * The attributes that should be appended to the model's array form.
     * Ini akan secara otomatis menambahkan 'foto_laporan_url' saat model diubah ke array/JSON.
     *
     * @var array
     */
    protected $appends = ['foto_laporan_url'];

    /**
     * Accessor untuk mendapatkan URL lengkap dari foto_laporan_path.
     *
     * @return string|null
     */
    public function getFotoLaporanUrlAttribute(): ?string
    {
        if ($this->foto_laporan_path) {
            return Storage::disk('public')->url($this->foto_laporan_path);
        }
        return null;
    }
        public function warga()
    {
        // Pastikan 'warga_id' adalah foreign key di tabel 'pengaduans'
        // dan 'id' adalah primary key di tabel 'wargas'
        return $this->belongsTo(Warga::class, 'warga_id', 'id');
    }



}