<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'pickup_points' yang menyimpan titik jemput dan titik turun penumpang
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'pickup_points'
    public function up(): void {
        Schema::create('pickup_points', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel routes; jika rute dihapus, titik terkait ikut terhapus otomatis (cascadeOnDelete)
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            // Nama lokasi titik dan alamat lengkapnya
            $table->string('nama_titik');
            $table->text('alamat');
            // Jenis titik: untuk penjemputan ('jemput') atau penurunan ('turun')
            $table->enum('tipe', ['jemput', 'turun']);
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'pickup_points'
    public function down(): void {
        Schema::dropIfExists('pickup_points');
    }
};