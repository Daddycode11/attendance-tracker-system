<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\AutoAbsent;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Run auto absent detection every day at 6 PM
        $schedule->command('auto:absent')->dailyAt('18:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}