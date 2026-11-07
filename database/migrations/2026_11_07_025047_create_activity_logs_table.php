
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Pengguna yang melakukan aktivitas
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Sekolah yang mengalami perubahan
            $table->foreignId('sekolah_id')
                ->nullable()
                ->constrained('sekolahs')
                ->nullOnDelete();

            // Jenis aktivitas: created, updated, deleted
            $table->string('action');

            // Data sebelum dan sesudah perubahan
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
