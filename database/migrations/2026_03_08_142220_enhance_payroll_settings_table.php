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
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->integer('working_days_per_month')->default(22)->after('id');
            $table->integer('working_hours_per_day')->default(8)->after('working_days_per_month');
            $table->decimal('ot_rate_multiplier', 4, 2)->default(1.25)->after('working_hours_per_day');
            $table->integer('late_grace_minutes')->default(0)->after('ot_rate_multiplier');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->dropColumn(['working_days_per_month', 'working_hours_per_day', 'ot_rate_multiplier', 'late_grace_minutes']);
        });
    }
};
