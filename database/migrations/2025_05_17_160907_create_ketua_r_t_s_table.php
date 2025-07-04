<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ketua_rt', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (bigint unsigned)
            $table->string('nama', 100); // varchar(100)
            $table->string('no_hp', 20); // varchar(20)
            $table->string('foto', 255)->nullable(); // varchar(255), nullable
            $table->year('periode_mulai'); // year
            $table->year('periode_selesai'); // year
            $table->text('Maps_link')->nullable(); // text, nullable
            $table->timestamps(); // created_at and updated_at timestamps, both nullable by default
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketua_rt');
    }
};

