<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model {
    use HasFactory;
    protected $guarded = [];

    public function route() {
        return $this->belongsTo(Route::class);
    }
    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }
    public function seats() {
        return $this->hasMany(Seat::class);
    }
    public function tickets() {
        return $this->hasMany(Ticket::class);
    }
}