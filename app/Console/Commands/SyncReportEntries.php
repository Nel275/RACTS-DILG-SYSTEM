<?php

namespace App\Console\Commands;

use App\Services\ReportEntryGenerator;
use Illuminate\Console\Command;

/**
 * Creates this period's My Reports entries for every recurring report
 * requirement and sends due-soon / overdue reminders. Safe to run any
 * number of times - see ReportEntryGenerator.
 */
class SyncReportEntries extends Command
{
    protected $signature = 'racts:sync-report-entries';

    protected $description = 'Generate monthly/annual My Reports entries and send deadline reminders';

    public function handle(ReportEntryGenerator $generator): int
    {
        $created = $generator->generate();
        $sent = $generator->sendReminders();

        $this->info("Created {$created} report entries; sent {$sent} reminders.");

        return self::SUCCESS;
    }
}
