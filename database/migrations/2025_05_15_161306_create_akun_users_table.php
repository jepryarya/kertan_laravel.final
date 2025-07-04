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
        Schema::create('akun_user', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED, No, AUTO_INCREMENT
            $table->string('name'); // varchar(255), No
            $table->string('email')->unique(); // varchar(255), No, unique
            $table->string('password'); // varchar(255), No
            $table->enum('role', ['admin1', 'admin2', 'user']); // enum('admin1', 'admin2', 'user'), No
            $table->rememberToken(); // varchar(100), Yes, NULL
            $table->timestamps(); // created_at timestamp, Yes, NULL; updated_at timestamp, Yes, NULL
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun_user');
    }
};

