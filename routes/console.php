<?php

use Illuminate\Support\Facades\Schedule;

// ── Auto mark absent employees every day at 6:00 PM ──
// Make sure cron is running on your server:
//   * * * * * php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1

Schedule::command('auto:absent')->dailyAt('18:00');