<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pickup_points', function (Blueprint $table) {
            $table->string('kota', 100)->nullable()->after('id');
            $table->unsignedBigInteger('route_id')->nullable()->change();
            $table->string('tipe', 20)->default('jemput')->change();
        });

        // Populate existing records with city from route
        DB::statement("
            UPDATE pickup_points p
            JOIN routes r ON p.route_id = r.id
            SET p.kota = CASE
                WHEN p.tipe = 'jemput' THEN r.kota_asal
                WHEN p.tipe = 'turun' THEN r.kota_tujuan
                ELSE r.kota_asal
            END
            WHERE p.kota IS NULL OR p.kota = ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_points', function (Blueprint $table) {
            $table->dropColumn('kota');
        });
    }
};
