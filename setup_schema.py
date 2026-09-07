import os
import glob

migrations_dir = 'database/migrations/'
models_dir = 'app/Models/'

migrations = {
    'routes': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('kota_asal');
            $table->string('kota_tujuan');
            $table->decimal('harga', 10, 2);
            $table->integer('estimasi_durasi_menit');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('routes');
    }
};""",
    'pickup_points': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('pickup_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->string('nama_titik');
            $table->text('alamat');
            $table->enum('tipe', ['jemput', 'turun']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('pickup_points');
    }
};""",
    'vehicles': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['Hiace', 'Minibus']);
            $table->string('plat_nomor')->unique();
            $table->integer('kapasitas_kursi');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('vehicles');
    }
};""",
    'schedules': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->dateTime('waktu_berangkat');
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('schedules');
    }
};""",
    'seats': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_kursi');
            $table->enum('status', ['available', 'locked', 'booked'])->default('available');
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('seats');
    }
};""",
    'tickets': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pickup_point_id')->constrained('pickup_points')->cascadeOnDelete();
            $table->foreignId('dropoff_point_id')->constrained('pickup_points')->cascadeOnDelete();
            $table->string('nama_penumpang');
            $table->string('qr_token')->unique();
            $table->enum('status', ['pending', 'paid', 'boarded', 'expired', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};""",
    'transactions': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('idempotency_key')->unique();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};""",
    'boarding_logs': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('boarding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('scan_time');
            $table->enum('result', ['accepted', 'rejected']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('boarding_logs');
    }
};"""
}

models = {
    'Route.php': """<?php
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
}""",
    'PickupPoint.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model {
    use HasFactory;
    protected $guarded = [];

    public function route() {
        return $this->belongsTo(Route::class);
    }
}""",
    'Vehicle.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model {
    use HasFactory;
    protected $guarded = [];

    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
}""",
    'Schedule.php': """<?php
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
}""",
    'Seat.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model {
    use HasFactory;
    protected $guarded = [];

    public function schedule() {
        return $this->belongsTo(Schedule::class);
    }
    public function ticket() {
        return $this->hasOne(Ticket::class);
    }
}""",
    'Ticket.php': """<?php
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
}""",
    'Transaction.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    use HasFactory;
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}""",
    'BoardingLog.php': """<?php
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
}"""
}

# Update migrations
for pattern, content in migrations.items():
    files = glob.glob(f"{migrations_dir}*create_{pattern}_table.php")
    if files:
        with open(files[0], 'w') as f:
            f.write(content)

# Update models
for file, content in models.items():
    with open(f"{models_dir}{file}", 'w') as f:
        f.write(content)

print("Schema generated successfully.")
