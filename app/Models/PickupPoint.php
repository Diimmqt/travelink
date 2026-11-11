<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel pickup_points yang menyimpan data titik penjemputan atau pengantaran penumpang
class PickupPoint extends Model {
    use HasFactory;
    // Mengizinkan seluruh kolom diisi secara massal
    protected $guarded = [];

    // Relasi bahwa setiap titik jemput/antar termasuk ke dalam satu rute perjalanan; mengembalikan relasi BelongsTo
    public function route() {
        return $this->belongsTo(Route::class);
    }
}