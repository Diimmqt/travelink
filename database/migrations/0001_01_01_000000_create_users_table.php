<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi awal untuk membuat tabel users, password_reset_tokens, dan sessions
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Menjalankan migrasi: membuat tabel pengguna, token reset password, dan penyimpanan sesi login
    public function up(): void
    {
        // Tabel utama 'users' untuk menyimpan data akun pengguna (admin maupun pembeli)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // Peran akun: 'admin' atau 'pembeli' (default: pembeli)
            $table->enum('role', ['admin', 'pembeli'])->default('pembeli');
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel untuk menyimpan token sementara saat pengguna meminta reset kata sandi
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel untuk menyimpan data sesi login pengguna ketika driver session menggunakan database
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Membatalkan migrasi: menghapus tabel users, password_reset_tokens, dan sessions
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
