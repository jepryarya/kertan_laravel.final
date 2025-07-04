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
        Schema::create('tamu_pendatang', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED, No, AUTO_INCREMENT
            $table->string('nama', 100); // varchar(100), No
            $table->string('no_identitas', 50); // varchar(50), No
            $table->string('foto_ktp', 255)->nullable(); // varchar(255), Yes, NULL
            $table->text('alamat_asal'); // text, No
            $table->string('ke_rumah', 100); // varchar(100), No
            $table->text('alasan_kunjungan'); // text, No
            $table->dateTime('waktu_masuk'); // datetime, No
            $table->dateTime('waktu_keluar')->nullable(); // datetime, Yes, NULL
            $table->enum('status', ['masuk', 'keluar'])->default('masuk'); // enum('masuk', 'keluar'), No, default 'masuk'
            $table->timestamps(); // created_at timestamp, Yes, NULL; updated_at timestamp, Yes, NULL
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('tamu_pendatang');
    }
};

