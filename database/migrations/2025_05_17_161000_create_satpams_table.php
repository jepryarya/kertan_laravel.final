<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Pastikan tabel 'satpam' belum ada sebelum mencoba membuatnya
        if (!Schema::hasTable('satpam')) {
            Schema::create('satpam', function (Blueprint $table) {
                $table->id(); // bigint UNSIGNED, AUTO_INCREMENT, primary key
                $table->string('nama', 100); // varchar(100), tidak nullable
                $table->string('no_hp', 20); // varchar(20), tidak nullable
                $table->string('foto_satpam', 255)->nullable(); // varchar(255), bisa NULL
                $table->enum('shift', ['pagi', 'siang', 'malam']); // enum('pagi', 'siang', 'malam')

                // Kolom foreign key akun_user_id
                // Kita akan menggunakan metode eksplisit seperti di migrasi 'warga'
                $table->unsignedBigInteger('akun_user_id'); // Definisi kolom numerik untuk FK
                $table->foreign('akun_user_id')->references('id')->on('akun_user')->onDelete('cascade'); // Definisi foreign key

                $table->timestamps(); // Menambahkan created_at dan updated_at, keduanya nullable
            });
        }
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        // Hapus tabel 'satpam' jika ada saat migrasi dibalikkan
        Schema::dropIfExists('satpam');
    }
};