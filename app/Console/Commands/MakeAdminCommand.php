<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {nama?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat akun admin baru untuk Travelink';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nama = $this->argument('nama') ?? $this->ask('Masukkan Nama Admin');
        $email = $this->argument('email') ?? $this->ask('Masukkan Email Admin');
        $password = $this->argument('password') ?? $this->secret('Masukkan Password Admin');

        $validator = Validator::make([
            'nama' => $nama,
            'email' => $email,
            'password' => $password,
        ], [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("Error: {$error}");
            }
            return Command::FAILURE;
        }

        $user = User::create([
            'nama' => $nama,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("Berhasil! Akun Admin '{$user->nama}' ({$user->email}) telah dibuat.");
        return Command::SUCCESS;
    }
}
