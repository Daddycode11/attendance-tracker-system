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
        Schema::table('holidays', function (Blueprint $table) {
            if (!Schema::hasColumn('holidays', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('holidays', 'date')) {
                $table->date('date')->unique()->after('name');
            }
            if (!Schema::hasColumn('holidays', 'type')) {
                $table->enum('type', ['Regular', 'Special'])->default('Regular')->after('date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn(['name', 'date', 'type']);
        });
    }
};
