<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ══════════════════════════════════════════════════════════════
//  SAVE THIS FILE AS:
//  database/migrations/2026_03_06_999998_fix_users_table.php
//
//  Then run:  php artisan migrate
//  Then run:  php artisan db:seed --class=AdminSeeder
// ══════════════════════════════════════════════════════════════

return new class extends Migration
{
    public function up(): void
    {
        // Drop the default Laravel users table and rebuild it
        // with username-based auth (no email needed)
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('username', 60)->unique();
            $table->string('password');
            $table->enum('role', ['Admin', 'Employee'])->default('Employee');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');

        // Restore original Laravel users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
};