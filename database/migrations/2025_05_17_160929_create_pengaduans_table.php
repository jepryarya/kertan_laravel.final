<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Perlu jika Anda menggunakan DB::statement

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED, Primary Key, AUTO_INCREMENT
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade'); // bigint UNSIGNED, Foreign Key

            // Kolom 'kategori' sesuai spesifikasi: varchar(255), No (NOT NULL)
            // Collation akan diambil dari konfigurasi default Laravel yang sudah kita ubah ke utf8mb4_unicode_ci
            $table->string('kategori', 255);

            // Kolom 'isi_pengaduan' sesuai spesifikasi: text, No (NOT NULL)
            // Collation akan diambil dari konfigurasi default
            $table->text('isi_pengaduan');

            // Kolom 'foto_laporan_path' sesuai spesifikasi: varchar(255), Yes (NULL)
            // Collation akan diambil dari konfigurasi default
            $table->string('foto_laporan_path', 255)->nullable();

            // Kolom 'status' sesuai spesifikasi: enum('menunggu', 'diproses', 'selesai'), No (NOT NULL), Default 'menunggu'
            // Laravel's enum() method akan menanganinya
            $table->enum('status', ['menunggu', 'diproses', 'selesai'])->default('menunggu');

            // Kolom 'created_at' dan 'updated_at' sesuai spesifikasi: timestamp, Yes (NULL)
            $table->timestamps();
        });

        // Catatan: Jika server database Anda adalah MySQL 5.7 atau versi lama yang
        // tidak mendukung ENUM di-migrate dengan $table->enum() Laravel
        // (kadang terjadi di beberapa server lama), Anda BISA menggunakan DB::statement ini.
        // Namun, coba dulu tanpa DB::statement karena $table->enum() harusnya bekerja.
        // Jika error terkait ENUM, baru tambahkan baris di bawah ini dan komentari $table->enum() di atas.
        // DB::statement("ALTER TABLE pengaduan MODIFY COLUMN status ENUM('menunggu', 'diproses', 'selesai') NOT NULL DEFAULT 'menunggu'");
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};