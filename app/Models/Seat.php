<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model Eloquent untuk tabel seats yang merepresentasikan kursi pada setiap jadwal perjalanan
class Seat extends Model
{
    use HasFactory;

    // Mengizinkan seluruh kolom diisi secara massal
    protected $guarded = [];
    // Mengonversi otomatis kolom locked_until menjadi objek tanggal/waktu (Carbon)
    protected $casts = [
        'locked_until' => 'datetime',
    ];

    // Relasi bahwa setiap kursi terikat pada satu jadwal perjalanan; mengembalikan relasi BelongsTo
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // Relasi one-to-one untuk mengambil satu tiket yang terkait dengan kursi ini; mengembalikan relasi HasOne
    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    // Relasi one-to-many untuk seluruh riwayat tiket pada kursi ini (termasuk yang gagal/batal); mengembalikan relasi HasMany
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Compute effective status considering active tickets and lock expiration.
     */
    // Accessor untuk menghitung status kursi yang sebenarnya ($seat->effective_status)
    // berdasarkan tiket aktif maupun batas waktu penguncian sementara; mengembalikan string ('booked'|'locked'|'available')
    public function getEffectiveStatusAttribute(): string
    {
        // 1. Paid or boarded ticket -> booked
        // Langkah 1: Cek di database apakah kursi ini punya tiket yang sudah dibayar ('paid') atau sudah naik ('boarded')
        $hasPaidTicket = Ticket::where('seat_id', $this->id)
            ->whereIn('status', ['paid', 'boarded'])
            ->exists();

        // Jika ada tiket lunas/boarded atau status kolom kursi sudah 'booked', kursi dianggap terisi permanen
        if ($hasPaidTicket || $this->status === 'booked') {
            return 'booked';
        }

        // 2. Active pending ticket (created in the last 10 minutes) -> locked
        // Langkah 2: Cek apakah ada tiket berstatus 'pending' yang dibuat dalam 10 menit terakhir (menunggu pembayaran)
        $hasPendingTicket = Ticket::where('seat_id', $this->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        // Jika masih ada transaksi pending yang aktif, kursi dianggap sedang dikunci sementara
        if ($hasPendingTicket) {
            return 'locked';
        }

        // 3. Unexpired lock -> locked
        // Langkah 3: Jika kolom status kursi adalah 'locked' dan waktu locked_until belum lewat dari sekarang, kursi masih terkunci
        if ($this->status === 'locked' && $this->locked_until && $this->locked_until >= now()) {
            return 'locked';
        }

        // Jika semua kondisi di atas tidak terpenuhi, berarti kursi tersedia untuk dipesan
        return 'available';
    }

    /**
     * Check if seat is available for booking.
     */
    // Mengecek apakah kursi ini boleh dipesan saat ini berdasarkan status efektifnya; mengembalikan bool
    public function isAvailable(): bool
    {
        return $this->effective_status === 'available';
    }
}