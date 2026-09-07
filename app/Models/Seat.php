<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Compute effective status considering active tickets and lock expiration.
     */
    public function getEffectiveStatusAttribute(): string
    {
        // 1. Paid or boarded ticket -> booked
        $hasPaidTicket = Ticket::where('seat_id', $this->id)
            ->whereIn('status', ['paid', 'boarded'])
            ->exists();

        if ($hasPaidTicket || $this->status === 'booked') {
            return 'booked';
        }

        // 2. Active pending ticket (created in the last 10 minutes) -> locked
        $hasPendingTicket = Ticket::where('seat_id', $this->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($hasPendingTicket) {
            return 'locked';
        }

        // 3. Unexpired lock -> locked
        if ($this->status === 'locked' && $this->locked_until && $this->locked_until >= now()) {
            return 'locked';
        }

        return 'available';
    }

    /**
     * Check if seat is available for booking.
     */
    public function isAvailable(): bool
    {
        return $this->effective_status === 'available';
    }
}