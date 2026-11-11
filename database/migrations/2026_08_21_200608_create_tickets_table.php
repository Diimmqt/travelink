<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'tickets' yang menyimpan data e-tiket penumpang
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'tickets'
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            // Relasi ke jadwal, kursi, dan pengguna (pembeli)
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Relasi ke titik penjemputan dan titik penurunan penumpang (keduanya merujuk tabel pickup_points)
            $table->foreignId('pickup_point_id')->constrained('pickup_points')->cascadeOnDelete();
            $table->foreignId('dropoff_point_id')->constrained('pickup_points')->cascadeOnDelete();
            // Nama penumpang yang tertera pada tiket
            $table->string('nama_penumpang');
            // Kode token unik untuk QR Code yang dipindai saat check-in/boarding
            $table->string('qr_token')->unique();
            // Status tiket sepanjang alur pemesanan hingga selesai atau refund
            $table->enum('status', ['pending', 'paid', 'boarded', 'expired', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'tickets'
    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};