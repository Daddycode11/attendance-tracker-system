<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ══════════════════════════════════════════════════════════════
//  SAVE THIS FILE AS:
//  database/migrations/2026_03_06_999999_fix_departments_positions_tables.php
//
//  Then run:  php artisan migrate
//  This will drop and recreate departments + positions with
//  the correct 'name' column that the seeder expects.
// ══════════════════════════════════════════════════════════════

return new class extends Migration
{
    public function up(): void
    {
        // ── Fix departments table ──────────────────────────────
        Schema::dropIfExists('departments');
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // ── Fix positions table ────────────────────────────────
        Schema::dropIfExists('positions');
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
        Schema::dropIfExists('positions');
    }
};