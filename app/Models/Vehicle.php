<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel vehicles yang menyimpan data armada/kendaraan travel
class Vehicle extends Model {
    use HasFactory;
    // Mengizinkan pengisian massal untuk seluruh kolom
    protected $guarded = [];

    // Relasi one-to-many: satu kendaraan dapat digunakan untuk banyak jadwal keberangkatan; mengembalikan relasi HasMany
    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
}