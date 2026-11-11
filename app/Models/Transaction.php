<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel transactions yang menyimpan data pembayaran tiket
class Transaction extends Model {
    use HasFactory;
    // Mengizinkan pengisian massal untuk seluruh kolom
    protected $guarded = [];

    // Relasi bahwa setiap transaksi dimiliki oleh satu pengguna (pembeli); mengembalikan relasi BelongsTo
    public function user() {
        return $this->belongsTo(User::class);
    }
    // Relasi one-to-many jika satu transaksi membayar beberapa tiket sekaligus; mengembalikan relasi HasMany
    public function tickets() {
        return $this->hasMany(Ticket::class);
    }
    // Relasi ke satu tiket utama melalui kolom ticket_id (untuk kompatibilitas pemesanan satu tiket); mengembalikan relasi BelongsTo
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}