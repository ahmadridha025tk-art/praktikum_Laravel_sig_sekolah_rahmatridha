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
    Schema::table('users', function (Blueprint $table) {
        $table->string('role', 20)->default('sekolah')->after('password');
        $table->foreignId('sekolah_id')->nullable()->after('role')
              ->constrained('sekolahs')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId('sekolah_id');
        $table->dropColumn('role');
    });
}
};
