<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pending', 'paid', 'boarded', 'expired', 'refund_requested', 'refunded') DEFAULT 'pending'");
    }

    public function down(): void {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pending', 'paid', 'boarded', 'expired', 'refunded') DEFAULT 'pending'");
    }
};
