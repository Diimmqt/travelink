<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Model autentikasi utama untuk pengguna aplikasi (Admin, Kasir, dan Pelanggan)
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Daftar kolom yang boleh diisi secara massal melalui User::create() atau update()
    protected $fillable = [
        'nama',
        'name',
        'email',
        'phone',
        'role',
        'password',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    // Mengecek apakah pengguna memiliki peran sebagai admin utama; mengembalikan bool
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Mengecek apakah pengguna memiliki peran sebagai kasir; mengembalikan bool
    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    // Mengecek apakah pengguna adalah pelanggan (mendukung nilai role 'cust' maupun 'pembeli'); mengembalikan bool
    public function isCust(): bool
    {
        return $this->role === 'cust' || $this->role === 'pembeli';
    }

    // Mutator: saat properti $user->name diisi, nilainya otomatis disimpan ke kolom 'nama' di database
    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    // Accessor: saat membaca $user->name, ambil nilai dari kolom 'nama' atau fallback ke 'name' jika ada
    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? ($this->attributes['name'] ?? null);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // Kolom sensitif yang disembunyikan saat model diubah menjadi array atau JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // Mengatur konversi tipe data otomatis: tanggal verifikasi menjadi datetime dan password otomatis di-hash
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // Relasi one-to-many: satu pengguna dapat memiliki banyak tiket perjalanan; mengembalikan relasi HasMany
    public function tickets()
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }

    // Relasi one-to-many: satu pengguna dapat memiliki banyak riwayat transaksi; mengembalikan relasi HasMany
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }
}
