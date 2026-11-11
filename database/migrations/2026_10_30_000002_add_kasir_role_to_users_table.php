<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Migrasi untuk memperbarui daftar role pengguna menjadi 'admin', 'kasir', dan 'cust' serta menambah kolom kontak telepon
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Menjalankan migrasi: memperluas ENUM role, memindahkan data 'pembeli' ke 'cust', mengunci ENUM baru, dan menambah kolom phone
    public function up(): void
    {
        // 1. Perluas enum terlebih dahulu agar menampung 'cust' dan 'kasir'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'cust', 'pembeli') NOT NULL DEFAULT 'cust'");

        // 2. Migrasikan role lama 'pembeli' menjadi 'cust'
        DB::table('users')->where('role', 'pembeli')->update(['role' => 'cust']);

        // 3. Kunci kembali enum hanya pada 3 role: admin, kasir, cust
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'cust') NOT NULL DEFAULT 'cust'");

        // 4. Tambahkan kolom phone jika belum ada untuk profil/kontak kasir
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 25)->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    // Membatalkan migrasi: menghapus kolom phone dan mengembalikan daftar role serta data pengguna ke format lama ('admin' dan 'pembeli')
    public function down(): void
    {
        // Menghapus kolom phone jika kolom tersebut ada di tabel users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });

        // Mengembalikan definisi ENUM role dan mengubah pengguna ber-role 'cust' atau 'kasir' kembali menjadi 'pembeli'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pembeli') NOT NULL DEFAULT 'pembeli'");
        DB::table('users')->where('role', 'cust')->orWhere('role', 'kasir')->update(['role' => 'pembeli']);
    }
};
