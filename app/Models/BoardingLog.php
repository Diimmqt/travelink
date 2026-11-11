<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel boarding_logs yang mencatat riwayat check-in / naik kendaraan penumpang
class BoardingLog extends Model {
    use HasFactory;
    // Mengizinkan seluruh kolom diisi secara massal (tidak ada kolom yang diblokir)
    protected $guarded = [];

    // Relasi bahwa setiap catatan boarding terhubung ke satu tiket tertentu; mengembalikan relasi BelongsTo
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
    // Relasi ke model User (kolom admin_id) untuk mengetahui admin/petugas yang melakukan validasi boarding; mengembalikan relasi BelongsTo
    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}