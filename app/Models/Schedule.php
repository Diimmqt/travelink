<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel schedules yang merepresentasikan jadwal keberangkatan armada
class Schedule extends Model {
    use HasFactory;
    // Mengizinkan seluruh kolom diisi secara massal
    protected $guarded = [];

    // Relasi bahwa setiap jadwal milik satu rute tertentu; mengembalikan relasi BelongsTo
    public function route() {
        return $this->belongsTo(Route::class);
    }
    // Relasi bahwa setiap jadwal menggunakan satu kendaraan/armada; mengembalikan relasi BelongsTo
    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }
    // Relasi one-to-many: satu jadwal memiliki banyak daftar kursi penumpang; mengembalikan relasi HasMany
    public function seats() {
        return $this->hasMany(Seat::class);
    }
    // Relasi one-to-many: satu jadwal memiliki banyak tiket yang dipesan; mengembalikan relasi HasMany
    public function tickets() {
        return $this->hasMany(Ticket::class);
    }
}