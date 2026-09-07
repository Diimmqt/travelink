<?php
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
};