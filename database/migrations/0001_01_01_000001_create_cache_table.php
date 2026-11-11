<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi untuk membuat tabel penyimpanan cache dan lock di database
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Menjalankan migrasi: membuat tabel 'cache' dan 'cache_locks'
    public function up(): void
    {
        // Tabel 'cache' untuk menyimpan data cache berbasis pasangan key-value beserta waktu kedaluwarsanya
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Tabel 'cache_locks' untuk mencegah proses bersamaan (race condition) dengan mekanisme penguncian atomik
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    // Membatalkan migrasi: menghapus tabel 'cache' dan 'cache_locks'
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
