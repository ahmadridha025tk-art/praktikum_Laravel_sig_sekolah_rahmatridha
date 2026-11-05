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
    Schema::create('profil_sekolahs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sekolah_id')->unique()
              ->constrained('sekolahs')->cascadeOnDelete();
        $table->string('kepala_sekolah', 150)->nullable();
        $table->string('email', 150)->nullable();
        $table->string('website')->nullable();
        $table->unsignedSmallInteger('tahun_berdiri')->nullable();
        $table->string('akreditasi', 25)->nullable();
        $table->unsignedInteger('jumlah_siswa')->nullable();
        $table->unsignedInteger('jumlah_guru')->nullable();
        $table->text('deskripsi')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_sekolahs');
    }
};
