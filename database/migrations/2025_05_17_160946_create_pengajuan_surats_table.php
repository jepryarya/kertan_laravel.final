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
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade'); // Foreign key to 'warga' table
            $table->enum('jenis_surat', ['nikah', 'domisili', 'ktp', 'kk', 'lainnya']); // Enum for type of letter
            $table->text('keterangan')->nullable(); // Text column for description, can be null
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu'); // Enum for status with default
            $table->string('foto_surat_path', 255)->nullable(); // Varchar(255) for photo path, can be null
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};

