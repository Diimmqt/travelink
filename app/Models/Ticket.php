<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model {
    use HasFactory;
    protected $guarded = [];

    public function schedule() {
        return $this->belongsTo(Schedule::class);
    }
    public function seat() {
        return $this->belongsTo(Seat::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function pickupPoint() {
        return $this->belongsTo(PickupPoint::class, 'pickup_point_id');
    }
    public function dropoffPoint() {
        return $this->belongsTo(PickupPoint::class, 'dropoff_point_id');
    }
    public function transaction() {
        return $this->hasOne(Transaction::class);
    }
}