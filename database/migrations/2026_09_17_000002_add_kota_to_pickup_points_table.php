<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Migrasi untuk menambahkan kolom 'kota' pada tabel pickup_points dan melonggarkan relasi route_id serta tipe titik
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Menjalankan migrasi: menambah kolom 'kota', membuat 'route_id' boleh kosong, mengubah 'tipe' jadi string, lalu mengisi nilai kota awal
    public function up(): void
    {
        Schema::table('pickup_points', function (Blueprint $table) {
            // Menambahkan kolom 'kota' setelah kolom 'id' agar titik jemput bisa dikelompokkan per kota
            $table->string('kota', 100)->nullable()->after('id');
            // Mengizinkan route_id bernilai null agar titik jemput bisa bersifat master data per kota
            $table->unsignedBigInteger('route_id')->nullable()->change();
            // Mengubah tipe dari enum menjadi string agar bisa menampung nilai seperti 'keduanya'
            $table->string('tipe', 20)->default('jemput')->change();
        });

        // Populate existing records with city from route
        // Mengisi otomatis kolom 'kota' pada data yang sudah ada berdasarkan kota_asal atau kota_tujuan dari tabel routes
        DB::statement("
            UPDATE pickup_points p
            JOIN routes r ON p.route_id = r.id
            SET p.kota = CASE
                WHEN p.tipe = 'jemput' THEN r.kota_asal
                WHEN p.tipe = 'turun' THEN r.kota_tujuan
                ELSE r.kota_asal
            END
            WHERE p.kota IS NULL OR p.kota = ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    // Membatalkan migrasi: menghapus kolom 'kota' dari tabel pickup_points
    public function down(): void
    {
        Schema::table('pickup_points', function (Blueprint $table) {
            $table->dropColumn('kota');
        });
    }
};
