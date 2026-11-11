<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel tickets yang menyimpan data pemesanan tiket penumpang
class Ticket extends Model {
    use HasFactory;
    // Mengizinkan pengisian massal untuk seluruh kolom
    protected $guarded = [];

    // Relasi bahwa setiap tiket merujuk ke satu jadwal keberangkatan; mengembalikan relasi BelongsTo
    public function schedule() {
        return $this->belongsTo(Schedule::class);
    }
    // Relasi bahwa setiap tiket terikat pada satu kursi tertentu; mengembalikan relasi BelongsTo
    public function seat() {
        return $this->belongsTo(Seat::class);
    }
    // Relasi ke pemilik/pemesan tiket pada model User; mengembalikan relasi BelongsTo
    public function user() {
        return $this->belongsTo(User::class);
    }
    // Relasi ke titik penjemputan penumpang (model PickupPoint melalui kolom pickup_point_id); mengembalikan relasi BelongsTo
    public function pickupPoint() {
        return $this->belongsTo(PickupPoint::class, 'pickup_point_id');
    }
    // Relasi ke titik penurunan penumpang (model PickupPoint melalui kolom dropoff_point_id); mengembalikan relasi BelongsTo
    public function dropoffPoint() {
        return $this->belongsTo(PickupPoint::class, 'dropoff_point_id');
    }
    // Relasi one-to-one ke data transaksi pembayaran tiket ini; mengembalikan relasi HasOne
    public function transaction() {
        return $this->hasOne(Transaction::class);
    }
}