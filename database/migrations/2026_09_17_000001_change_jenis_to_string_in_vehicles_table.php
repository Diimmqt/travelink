<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Migrasi untuk mengubah kolom 'jenis' pada tabel vehicles dari ENUM menjadi STRING agar lebih fleksibel
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Menjalankan migrasi: mengubah tipe kolom 'jenis' menjadi string maksimal 100 karakter
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('jenis', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Membatalkan migrasi: mengembalikan tipe kolom 'jenis' menjadi ENUM('Hiace', 'Minibus')
    public function down(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY jenis ENUM('Hiace', 'Minibus') NOT NULL");
    }
};
