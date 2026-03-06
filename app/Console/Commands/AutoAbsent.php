<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;

class AutoAbsent extends Command
{
    /**
     * The name and signature of the console command.
     * Run with:  php artisan auto:absent
     */
    protected $signature   = 'auto:absent';
    protected $description = 'Mark absent for employees with no attendance record today';

    public function handle(): void
    {
        $today     = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;

        // ── Skip weekends ──────────────────────────
        if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
            $this->info("Skipped: {$today->format('l')} is a weekend.");
            return;
        }

        // ── Skip public holidays ───────────────────
        if (Holiday::whereDate('date', $today)->exists()) {
            $this->info("Skipped: {$today->format('Y-m-d')} is a public holiday.");
            return;
        }

        // ── Mark absent ────────────────────────────
        $employees = Employee::all();
        $marked    = 0;

        foreach ($employees as $emp) {
            $exists = Attendance::where('employee_id', $emp->id)
                        ->whereDate('date', $today)
                        ->exists();

            if (!$exists) {
                Attendance::create([
                    'employee_id' => $emp->id,
                    'date'        => $today->toDateString(),
                    'status'      => 'Absent',
                ]);
                $marked++;
            }
        }

        $this->info("✅ Auto-absent complete: {$marked} employee(s) marked Absent for {$today->format('F j, Y')}.");
    }
}