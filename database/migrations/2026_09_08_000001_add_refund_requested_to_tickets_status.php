<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Migrasi untuk menambahkan opsi status 'refund_requested' pada kolom status di tabel tickets
return new class extends Migration {
    // Menjalankan migrasi: mengubah definisi ENUM kolom status agar mendukung status pengajuan refund
    public function up(): void {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pending', 'paid', 'boarded', 'expired', 'refund_requested', 'refunded') DEFAULT 'pending'");
    }

    // Membatalkan migrasi: mengembalikan definisi ENUM kolom status ke daftar awal tanpa 'refund_requested'
    public function down(): void {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pending', 'paid', 'boarded', 'expired', 'refunded') DEFAULT 'pending'");
    }
};
