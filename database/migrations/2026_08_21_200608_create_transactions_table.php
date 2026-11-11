<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migrasi untuk membuat tabel 'transactions' yang mencatat data transaksi pembayaran
return new class extends Migration {
    // Menjalankan migrasi: membuat tabel 'transactions'
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Relasi ke pengguna pembayar dan tiket terkait
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            // Metode pembayaran yang digunakan (misalnya: bank_transfer, gopay, qris, dll.)
            $table->string('payment_method')->nullable();
            // Total nominal pembayaran (maksimal 10 digit dengan 2 angka desimal)
            $table->decimal('amount', 10, 2);
            // Status pembayaran transaksi
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            // Kunci unik (idempotency key) untuk mencegah duplikasi transaksi jika permintaan terkirim lebih dari sekali
            $table->string('idempotency_key')->unique();
            $table->timestamps();
        });
    }
    // Membatalkan migrasi: menghapus tabel 'transactions'
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};