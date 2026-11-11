<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel routes yang menyimpan data rute perjalanan (kota asal, tujuan, harga)
class Route extends Model {
    use HasFactory;
    // Mengizinkan pengisian massal untuk seluruh kolom pada tabel routes
    protected $guarded = [];

    // Relasi one-to-many: satu rute memiliki banyak titik penjemputan/pengantaran; mengembalikan relasi HasMany
    public function pickupPoints() {
        return $this->hasMany(PickupPoint::class);
    }
    // Relasi one-to-many: satu rute dapat memiliki banyak jadwal keberangkatan; mengembalikan relasi HasMany
    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
}