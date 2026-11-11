<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
// Factory untuk membuat data pengguna palsu (dummy) secara cepat saat testing atau seeding database
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    // Menyimpan hash password sementara agar tidak perlu melakukan hashing berulang kali saat membuat banyak user
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // Mendefinisikan nilai bawaan (default) untuk setiap kolom model User saat factory dipanggil; mengembalikan array pasangan kolom dan nilainya
    public function definition(): array
    {
        return [
            // Membuat nama acak menggunakan Faker
            'nama' => fake()->name(),
            // Membuat email unik acak yang aman untuk uji coba
            'email' => fake()->unique()->safeEmail(),
            // Menandai email langsung terverifikasi pada waktu sekarang
            'email_verified_at' => now(),
            // Menggunakan password hash yang sudah disimpan, atau membuat hash dari kata 'password' jika belum ada
            'password' => static::$password ??= Hash::make('password'),
            // Token acak 10 karakter untuk fitur "Remember Me"
            'remember_token' => Str::random(10),
            // Peran bawaan pengguna yang dibuat adalah 'pembeli'
            'role' => 'pembeli',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    // State tambahan untuk menghasilkan pengguna yang alamat emailnya belum terverifikasi; mengembalikan instance factory yang sudah dimodifikasi
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
