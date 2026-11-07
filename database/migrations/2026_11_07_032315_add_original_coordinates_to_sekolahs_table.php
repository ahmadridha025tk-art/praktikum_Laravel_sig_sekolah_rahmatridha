<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->decimal('latitude_awal', 10, 7)->nullable();
            $table->decimal('longitude_awal', 10, 7)->nullable();
        });

        // Simpan koordinat sekolah yang sudah ada sebagai titik awal
        DB::table('sekolahs')->update([
            'latitude_awal' => DB::raw('latitude'),
            'longitude_awal' => DB::raw('longitude'),
        ]);
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'latitude_awal',
                'longitude_awal',
            ]);
        });
    }
};
