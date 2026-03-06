<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ══════════════════════════════════════════════════════════════
//  SINGLE FILE — creates all attendance system tables.
//
//  OPTION A (recommended — fresh install):
//    1. Delete ALL existing migration files in database/migrations/
//       EXCEPT: 0001_01_01_000000_create_users_table.php (keep the original)
//    2. Save this file as:
//       database/migrations/2026_01_01_000001_create_attendance_system_tables.php
//    3. Run:  php artisan migrate:fresh
//
//  OPTION B (existing project — keep existing tables):
//    1. Save this file as:
//       database/migrations/2026_01_01_000001_create_attendance_system_tables.php
//    2. Run:  php artisan migrate
//       (It will skip tables that already exist)
// ══════════════════════════════════════════════════════════════

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. employees ──────────────────────────────────────
        // Department and position are plain text columns — no separate table needed.
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('employee_id', 30)->unique();
                $table->string('name', 100);
                $table->string('department', 60)->nullable();
                $table->string('position', 60)->nullable();
                $table->decimal('basic_salary', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // ── 2. users ─────────────────────────────────────────
        // Drop and recreate with username-based auth (no email).
        // ⚠ If you already have users data you want to keep, use OPTION B
        //   and manually alter the table instead of recreating it.
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                  ->nullable()
                  ->constrained('employees')
                  ->nullOnDelete();
            $table->string('username', 60)->unique();
            $table->string('password');
            $table->enum('role', ['Admin', 'Employee'])->default('Employee');
            $table->rememberToken();
            $table->timestamps();
        });

        // ── 3. attendance ─────────────────────────────────────
        if (!Schema::hasTable('attendance')) {
            Schema::create('attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')
                      ->constrained('employees')
                      ->cascadeOnDelete();
                $table->date('date');
                $table->time('time_in_am')->nullable();
                $table->time('time_out_lunch')->nullable();
                $table->time('time_in_pm')->nullable();
                $table->time('time_out_final')->nullable();
                $table->unsignedInteger('late_minutes')->default(0);
                $table->unsignedInteger('undertime_minutes')->default(0);
                $table->unsignedInteger('overtime_minutes')->default(0);
                $table->enum('status', [
                    'Present', 'Late', 'Absent', 'Half-day', 'Incomplete'
                ])->default('Present');
                $table->timestamps();

                // One record per employee per day
                $table->unique(['employee_id', 'date']);
            });
        }

        // ── 4. payrolls ───────────────────────────────────────
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')
                      ->constrained('employees')
                      ->cascadeOnDelete();
                $table->date('month');                          // e.g. 2026-03-01
                $table->unsignedInteger('total_days_present')->default(0);
                $table->unsignedInteger('total_late_minutes')->default(0);
                $table->unsignedInteger('total_overtime_minutes')->default(0);
                $table->unsignedInteger('absent_days')->default(0);
                $table->decimal('basic_salary', 10, 2)->default(0);
                $table->decimal('overtime_pay', 10, 2)->default(0);
                $table->decimal('deductions', 10, 2)->default(0);
                $table->decimal('net_salary', 10, 2)->default(0);
                $table->timestamps();

                // One payroll record per employee per month
                $table->unique(['employee_id', 'month']);
            });
        }

        // ── 5. leaves ─────────────────────────────────────────
        if (!Schema::hasTable('leaves')) {
            Schema::create('leaves', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')
                      ->constrained('employees')
                      ->cascadeOnDelete();
                $table->enum('leave_type', ['Sick', 'Vacation', 'Others'])->default('Sick');
                $table->date('start_date');
                $table->date('end_date');
                $table->text('reason')->nullable();
                $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
                $table->timestamps();
            });
        }

        // ── 6. holidays ───────────────────────────────────────
        if (!Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name', 80);
                $table->date('date')->unique();
                $table->timestamps();
            });
        }

        // ── 7. departments (name column required) ─────────────
        // Used optionally for dropdowns — employees table stores dept as plain string.
        Schema::dropIfExists('departments');
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();       // ← this is the column the seeder needs
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // ── 8. positions (name column required) ──────────────
        Schema::dropIfExists('positions');
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();       // ← this is the column the seeder needs
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop in reverse order (foreign keys first)
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('users');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('positions');
    }
};