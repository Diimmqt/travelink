<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'seats' yang menyimpan status setiap kursi pada masing-masing jadwal
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'seats'
    public function up(): void {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            // Relasi ke jadwal keberangkatan terkait
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            // Label/nomor kursi (misalnya: A1, A2, dst.)
            $table->string('nomor_kursi');
            // Status kursi: tersedia ('available'), dikunci sementara saat proses checkout ('locked'), atau sudah dipesan ('booked')
            $table->enum('status', ['available', 'locked', 'booked'])->default('available');
            // Batas waktu penguncian sementara kursi sebelum otomatis dilepas kembali jika tidak dibayar
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'seats'
    public function down(): void {
        Schema::dropIfExists('seats');
    }
};