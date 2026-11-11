<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'vehicles' yang menyimpan data armada kendaraan travel
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'vehicles'
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            // Jenis armada awal yang didukung (Hiace atau Minibus)
            $table->enum('jenis', ['Hiace', 'Minibus']);
            // Nomor polisi kendaraan yang bersifat unik (tidak boleh ada dua kendaraan dengan plat yang sama)
            $table->string('plat_nomor')->unique();
            // Jumlah total kursi penumpang yang tersedia pada kendaraan
            $table->integer('kapasitas_kursi');
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'vehicles'
    public function down(): void {
        Schema::dropIfExists('vehicles');
    }
};