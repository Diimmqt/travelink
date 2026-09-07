<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingLog extends Model {
    use HasFactory;
    protected $guarded = [];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}