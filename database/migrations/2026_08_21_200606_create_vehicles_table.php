<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['Hiace', 'Minibus']);
            $table->string('plat_nomor')->unique();
            $table->integer('kapasitas_kursi');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('vehicles');
    }
};