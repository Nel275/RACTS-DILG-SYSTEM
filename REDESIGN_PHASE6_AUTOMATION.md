# RACTS scheduled tasks

Scheduled in `routes/console.php`:

| Command | When | What it does |
|---|---|---|
| `racts:check-late-submissions` | daily 01:00 | Compliance-grid alerts (period open, deadline today, late). |
| `racts:sync-report-entries` | daily 00:30 | Creates each new month's/year's My Reports entries from DILG's monthly/annual report requirements, marks overdue entries, and sends due-soon (3 days) and overdue reminders. |

Both commands can run more than once without side effects. Entries are unique per requirement, barangay and period. Each reminder is sent once per entry, and cleared notifications still count as sent. `withoutOverlapping()` stops two runs of `racts:sync-report-entries` from running at the same time.

My Reports and the barangay dashboard also generate any missing entries for that barangay when they load. A requirement is visible even before the scheduler has run. The scheduler is still needed for reminders.

## Running the scheduler

Laravel's scheduler only works if `php artisan schedule:run` runs every minute.

**Linux / Railway (cron):**

```
* * * * * cd /path/to/racts && php artisan schedule:run >> /dev/null 2>&1
```

On Railway, add a cron service or a worker running `php artisan schedule:work`.

**Windows / XAMPP (Task Scheduler):**

1. Create Task, then add a trigger: Daily, repeat every 1 minute for 1 day.
2. Action: Start a program.
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `artisan schedule:run`
   - Start in: `C:\xampp\htdocs\RACTS1`
3. Tick "Run whether user is logged on or not".

To run a command by hand: `php artisan racts:sync-report-entries`.

## Deploy checklist for this release

1. `composer install`. This adds `setasign/fpdi` and `setasign/fpdf`, which digital stamping needs.
2. `php artisan migrate`. This adds report-requirement scheduling, DILG review fields and digital stamps. The bad-word list ships in code (`app/Services/ProfanityDictionary.php`) and needs no setup.
3. Make sure the scheduler (above) is running.
