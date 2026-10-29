<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('sekolahs', function (Blueprint $table) {
        $table->id();
        $table->string('npsn', 8)->unique();
        $table->string('nama');
        $table->enum('jenjang', ['SD', 'SMP', 'SMA', 'SMK']);
        $table->enum('status', ['Negeri', 'Swasta']);
        $table->text('alamat');
        $table->string('kecamatan', 100);
        $table->string('telepon', 20)->nullable();
        $table->decimal('latitude', 10, 7);
        $table->decimal('longitude', 10, 7);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
