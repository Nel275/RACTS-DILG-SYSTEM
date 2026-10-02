<?php

namespace App\Console\Commands;

use App\Models\Barangay;
use App\Models\BarangayLogDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * One-time cleanup: moves report files saved under the old numbered
 * folders (incoming-reports/7, barangays/Malvar/...) into the named
 * Barangay/SK folders, and updates each entry's stored path to match.
 * Safe to run more than once - files already in the right place are skipped.
 *
 *   php artisan racts:organize-report-folders
 */
class OrganizeReportFolders extends Command
{
    protected $signature = 'racts:organize-report-folders';

    protected $description = 'Move uploaded report files into Barangay/SK folders named after each barangay';

    public function handle(): int
    {
        $disk = Storage::disk('local');

        Barangay::all()->each(fn (Barangay $barangay) => $barangay->ensureFolder());

        $moved = 0;
        $skipped = 0;
        $missing = 0;

        BarangayLogDetail::with(['barangay', 'barangayLog'])
            ->whereNotNull('report_file_path')
            ->get()
            ->each(function (BarangayLogDetail $entry) use ($disk, &$moved, &$skipped, &$missing) {
                $targetDir = $entry->isFiled()
                    ? $entry->barangay->storagePath().'/'.$entry->councilFolder()
                    : $entry->incomingFolder();
                $target = $targetDir.'/'.basename($entry->report_file_path);

                if ($entry->report_file_path === $target) {
                    $skipped++;

                    return;
                }

                if (! $disk->exists($entry->report_file_path)) {
                    $this->warn("Missing file for entry #{$entry->id}: {$entry->report_file_path}");
                    $missing++;

                    return;
                }

                $disk->makeDirectory($targetDir);
                $disk->move($entry->report_file_path, $target);
                $entry->update(['report_file_path' => $target]);
                $this->line("#{$entry->id}: {$target}");
                $moved++;
            });

        // Remove the old numbered staging folders once they're empty.
        foreach ($disk->directories('incoming-reports') as $dir) {
            if (ctype_digit(basename($dir)) && empty($disk->allFiles($dir))) {
                $disk->deleteDirectory($dir);
            }
        }

        $this->info("Done. Moved: {$moved}, already in place: {$skipped}, missing: {$missing}.");

        return self::SUCCESS;
    }
}
