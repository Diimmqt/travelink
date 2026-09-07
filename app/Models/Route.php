<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model {
    use HasFactory;
    protected $guarded = [];

    public function pickupPoints() {
        return $this->hasMany(PickupPoint::class);
    }
    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
}