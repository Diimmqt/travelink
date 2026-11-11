<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Perintah Artisan untuk membuka kembali kursi yang masa penguncian sementaranya sudah kedaluwarsa
class UnlockExpiredSeats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Nama perintah yang dapat dipanggil melalui terminal atau scheduler (`php artisan seats:unlock`)
    protected $signature = 'seats:unlock';

    /**
     * The console command description.
     *
     * @var string
     */
    // Deskripsi perintah yang menjelaskan tugas membuka kunci kursi yang sudah melewati batas waktu
    protected $description = 'Unlock seats that have exceeded their lock time';

    /**
     * Execute the console command.
     */
    // Menjalankan proses pembersihan kunci kursi yang kedaluwarsa dan menampilkan jumlah kursi yang dibuka
    public function handle()
    {
        // Cari semua kursi berstatus 'locked' yang batas waktunya (locked_until) sudah lewat dari waktu saat ini,
        // lalu ubah kembali statusnya menjadi 'available' dan kosongkan waktu pengunciannya
        $unlocked = \App\Models\Seat::where('status', 'locked')
            ->whereNotNull('locked_until')
            ->where('locked_until', '<', now())
            ->update([
                'status' => 'available',
                'locked_until' => null
            ]);
            
        // Tampilkan informasi jumlah total kursi yang berhasil dibuka kembali
        $this->info("Unlocked {$unlocked} seats.");
    }
}
