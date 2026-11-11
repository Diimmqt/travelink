<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'routes' yang menyimpan daftar rute perjalanan travel
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'routes'
    public function up(): void {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            // Nama kota keberangkatan dan kota tujuan
            $table->string('kota_asal');
            $table->string('kota_tujuan');
            // Harga tiket per kursi untuk rute ini (maksimal 10 digit dengan 2 angka desimal)
            $table->decimal('harga', 10, 2);
            // Perkiraan lama perjalanan dalam satuan menit
            $table->integer('estimasi_durasi_menit');
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'routes'
    public function down(): void {
        Schema::dropIfExists('routes');
    }
};