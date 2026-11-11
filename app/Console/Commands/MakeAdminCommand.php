<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// Perintah Artisan untuk membuat akun pengguna baru dengan hak akses admin melalui terminal
class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Nama perintah di terminal beserta argumen opsional: nama, email, dan password
    protected $signature = 'make:admin {nama?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    // Deskripsi singkat perintah yang muncul saat menjalankan `php artisan list`
    protected $description = 'Membuat akun admin baru untuk Travelink';

    /**
     * Execute the console command.
     */
    // Method utama yang dijalankan saat perintah `make:admin` dieksekusi; mengembalikan kode status berhasil/gagal
    public function handle()
    {
        // Ambil input dari argumen perintah, atau tanya interaktif ke pengguna jika tidak disertakan
        $nama = $this->argument('nama') ?? $this->ask('Masukkan Nama Admin');
        $email = $this->argument('email') ?? $this->ask('Masukkan Email Admin');
        // Gunakan secret() agar ketikan password disembunyikan di layar terminal
        $password = $this->argument('password') ?? $this->secret('Masukkan Password Admin');

        // Validasi data input sebelum disimpan agar format email benar, unik, dan password minimal 8 karakter
        $validator = Validator::make([
            'nama' => $nama,
            'email' => $email,
            'password' => $password,
        ], [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Jika ada aturan validasi yang tidak terpenuhi, tampilkan seluruh pesan kesalahan lalu batalkan proses
        if ($validator->fails()) {
            // Ulangi setiap pesan error dan cetak ke terminal dengan warna merah
            foreach ($validator->errors()->all() as $error) {
                $this->error("Error: {$error}");
            }
            return Command::FAILURE;
        }

        // Buat record pengguna baru di database dengan password yang sudah di-hash secara aman
        $user = User::create([
            'nama' => $nama,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        // Tetapkan role secara langsung menjadi 'admin' (di luar mass assignment) lalu simpan perubahan
        $user->role = 'admin';
        $user->save();

        // Tampilkan informasi keberhasilan ke terminal dan kembalikan status sukses
        $this->info("Berhasil! Akun Admin '{$user->nama}' ({$user->email}) telah dibuat.");
        return Command::SUCCESS;
    }
}
