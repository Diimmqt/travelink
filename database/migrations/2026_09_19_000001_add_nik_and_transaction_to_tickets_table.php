<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrasi untuk menambahkan kolom 'nik' dan 'transaction_id' pada tabel tickets serta membuat 'ticket_id' di transactions boleh kosong
return new class extends Migration {
    // Menjalankan migrasi: menambah kolom identitas penumpang (NIK) dan relasi ke satu transaksi induk (untuk pembelian multi-tiket)
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Cek terlebih dahulu agar tidak error jika kolom 'nik' sudah ada sebelumnya
            if (!Schema::hasColumn('tickets', 'nik')) {
                $table->string('nik', 30)->nullable()->after('nama_penumpang');
            }
            // Cek lalu tambahkan foreign key 'transaction_id'; jika transaksi dihapus, kolom ini diisi null (nullOnDelete)
            if (!Schema::hasColumn('tickets', 'transaction_id')) {
                $table->foreignId('transaction_id')->nullable()->after('dropoff_point_id')->constrained('transactions')->nullOnDelete();
            }
        });

        // Mengubah kolom 'ticket_id' pada tabel transactions agar boleh bernilai null (karena satu transaksi bisa mencakup banyak tiket)
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('ticket_id')->nullable()->change();
        });
    }

    // Membatalkan migrasi: menghapus foreign key dan kolom 'transaction_id' serta kolom 'nik' jika ada
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'transaction_id')) {
                $table->dropForeign(['transaction_id']);
                $table->dropColumn('transaction_id');
            }
            if (Schema::hasColumn('tickets', 'nik')) {
                $table->dropColumn('nik');
            }
        });
    }
};
