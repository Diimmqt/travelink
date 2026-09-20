<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'nik')) {
                $table->string('nik', 30)->nullable()->after('nama_penumpang');
            }
            if (!Schema::hasColumn('tickets', 'transaction_id')) {
                $table->foreignId('transaction_id')->nullable()->after('dropoff_point_id')->constrained('transactions')->nullOnDelete();
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('ticket_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'transaction_id')) {
                $table->dropForeign(['transaction_id']);
                $table->dropColumn('transaction_id');
            }
            if (Schema::hasColumn('tickets', 'nik')) {
                $table->dropColumn('nik');
            }
        });
    }
};
