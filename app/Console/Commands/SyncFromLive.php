<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Run on the office laptop: copies every barangay folder and report
 * file from the live site into this computer's
 * storage/app/private/barangays folder. New barangay folders are
 * created automatically; files that are already up to date are skipped.
 * Nothing on this computer is ever deleted.
 *
 *   php artisan racts:sync-from-live
 *
 * Needs SYNC_URL and SYNC_TOKEN in this computer's .env.
 */
class SyncFromLive extends Command
{
    protected $signature = 'racts:sync-from-live';

    protected $description = 'Download barangay folders and report files from the live RACTS site to this computer';

    public function handle(): int
    {
        $url = rtrim((string) config('services.racts_sync.url'), '/');
        $token = (string) config('services.racts_sync.token');

        if ($url === '' || $token === '') {
            $this->error('Set SYNC_URL and SYNC_TOKEN in this computer\'s .env file first.');

            return self::FAILURE;
        }

        $http = fn () => Http::withHeaders(['X-Sync-Token' => $token])->timeout(120);

        $response = $http()->get($url.'/sync/manifest');

        if (! $response->successful()) {
            $this->error('The live site refused the request (HTTP '.$response->status().'). Check SYNC_URL and that SYNC_TOKEN matches the one on Railway.');

            return self::FAILURE;
        }

        $disk = Storage::disk('local');
        $manifest = $response->json();

        $newFolders = 0;
        foreach ($manifest['folders'] ?? [] as $folder) {
            if ($this->isSafe($folder) && ! $disk->exists($folder)) {
                $disk->makeDirectory($folder);
                $this->line("Folder created: {$folder}");
                $newFolders++;
            }
        }

        $downloaded = 0;
        $upToDate = 0;
        foreach ($manifest['files'] ?? [] as $file) {
            $path = $file['path'] ?? '';
            if (! $this->isSafe($path)) {
                continue;
            }

            if ($disk->exists($path)
                && $disk->size($path) === ($file['size'] ?? null)
                && $disk->lastModified($path) >= ($file['modified'] ?? 0)
            ) {
                $upToDate++;

                continue;
            }

            $disk->makeDirectory(dirname($path));
            $target = $disk->path($path);

            $download = $http()->withOptions(['sink' => $target])
                ->get($url.'/sync/file', ['path' => $path]);

            if (! $download->successful()) {
                @unlink($target);
                $this->warn("Could not download {$path} (HTTP {$download->status()})");

                continue;
            }

            if (! empty($file['modified'])) {
                @touch($target, (int) $file['modified']);
            }

            $this->line("Downloaded: {$path}");
            $downloaded++;
        }

        $this->info("Sync complete. New folders: {$newFolders}, files downloaded: {$downloaded}, already up to date: {$upToDate}.");
        $this->line('Saved in: '.$disk->path('barangays'));

        return self::SUCCESS;
    }

    private function isSafe(string $path): bool
    {
        return str_starts_with($path, 'barangays/') && ! str_contains($path, '..');
    }
}
