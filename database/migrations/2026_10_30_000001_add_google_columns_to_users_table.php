<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi untuk menambahkan kolom integrasi login Google (google_id dan avatar) pada tabel users
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Menjalankan migrasi: menambah kolom google_id dan avatar serta mengizinkan password bernilai null bagi pengguna Google OAuth
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ID unik akun Google milik pengguna (boleh kosong bagi pengguna yang daftar manual)
            $table->string('google_id')->nullable()->unique()->after('email');
            // URL foto profil dari akun Google pengguna
            $table->string('avatar')->nullable()->after('google_id');
            // Password dibuat opsional (nullable) karena pengguna yang login lewat Google tidak membutuhkan password lokal
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Membatalkan migrasi: menghapus indeks unik google_id, menghapus kolom google_id dan avatar, serta mewajibkan password kembali
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn(['google_id', 'avatar']);
            $table->string('password')->nullable(false)->change();
        });
    }
};

