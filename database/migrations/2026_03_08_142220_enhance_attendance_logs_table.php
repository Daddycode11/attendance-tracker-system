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
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->foreignId('attendance_id')->after('id')->constrained('attendance')->cascadeOnDelete();
            $table->foreignId('user_id')->after('attendance_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['created', 'updated', 'deleted'])->after('user_id');
            $table->json('old_values')->nullable()->after('action');
            $table->json('new_values')->nullable()->after('old_values');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['attendance_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['attendance_id', 'user_id', 'action', 'old_values', 'new_values']);
        });
    }
};
