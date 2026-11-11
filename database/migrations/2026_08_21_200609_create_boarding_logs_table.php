<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'boarding_logs' yang mencatat riwayat pemindaian tiket saat penumpang naik
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'boarding_logs'
    public function up(): void {
        Schema::create('boarding_logs', function (Blueprint $table) {
            $table->id();
            // Relasi ke tiket yang dipindai dan petugas (admin/kasir pada tabel users) yang memindai
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            // Waktu saat pemindaian QR atau verifikasi boarding dilakukan
            $table->timestamp('scan_time');
            // Hasil validasi boarding: diterima ('accepted') atau ditolak ('rejected')
            $table->enum('result', ['accepted', 'rejected']);
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'boarding_logs'
    public function down(): void {
        Schema::dropIfExists('boarding_logs');
    }
};