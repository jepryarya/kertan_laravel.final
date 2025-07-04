<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Pastikan tabel 'warga' belum ada sebelum mencoba membuatnya
        if (!Schema::hasTable('warga')) {
            Schema::create('warga', function (Blueprint $table) {
                $table->id(); // bigint UNSIGNED, AUTO_INCREMENT, primary key
                $table->string('nama', 100); // varchar(100), tidak nullable
                $table->string('nik', 16); // varchar(16), tidak nullable
                $table->string('kk', 16); // varchar(16), tidak nullable
                $table->text('alamat_rumah'); // text, tidak nullable
                $table->string('no_rumah', 10); // varchar(10), tidak nullable
                $table->string('no_hp', 20); // varchar(20), tidak nullable
                $table->string('foto_rumah', 255)->nullable(); // varchar(255), bisa NULL
                $table->integer('jumlah_anggota_keluarga')->nullable(); // int, bisa NULL

                // Kolom foreign key akun_user_id
                // Pastikan tabel 'akun_user' sudah ada sebelum migrasi ini dijalankan
                $table->unsignedBigInteger('akun_user_id'); 
                $table->foreign('akun_user_id')->references('id')->on('akun_user')->onDelete('cascade');

                $table->timestamps(); // Menambahkan created_at dan updated_at, keduanya nullable
            });
        }
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        // Hapus tabel 'warga' jika ada saat migrasi dibalikkan
        Schema::dropIfExists('warga');
    }
};