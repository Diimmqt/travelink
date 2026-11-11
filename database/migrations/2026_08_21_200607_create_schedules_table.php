<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'schedules' yang menyimpan jadwal keberangkatan travel
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'schedules'
    public function up(): void {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // Relasi ke rute perjalanan dan kendaraan yang bertugas (hapus otomatis jika induknya dihapus)
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            // Tanggal dan jam keberangkatan yang dijadwalkan
            $table->dateTime('waktu_berangkat');
            // Status jadwal: terjadwal, sedang berjalan, selesai, atau dibatalkan
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'schedules'
    public function down(): void {
        Schema::dropIfExists('schedules');
    }
};