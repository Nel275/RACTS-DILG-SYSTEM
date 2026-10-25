<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| RACTS Automation (client spec Section 4)
|--------------------------------------------------------------------------
|
| racts:check-late-submissions decides for itself whether there's
| anything to do (it no-ops before the 6th of the month - see the
| command's own doc comment), so it's safe to schedule daily rather
| than trying to target one exact date.
|
| This only fires if something is actually calling `php artisan
| schedule:run` on a timer. XAMPP on Windows has no cron, so that's a
| Windows Task Scheduler task - see REDESIGN_PHASE6_AUTOMATION.md for
| the exact setup steps.
|
*/
Schedule::command('racts:check-late-submissions')->dailyAt('01:00');

// My Reports automation: creates each new month's/year's report entries
// from the DILG's recurring requirements and sends due-soon/overdue
// reminders. Idempotent, so running it more than once a day is harmless;
// withoutOverlapping() keeps two runs from racing each other. Needs the
// same `php artisan schedule:run` timer as the command above.
Schedule::command('racts:sync-report-entries')->dailyAt('00:30')->withoutOverlapping();
