<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Lets the office laptop mirror the live site's barangay folders and
 * report files (see the racts:sync-from-live command). Read-only, and
 * limited to the barangays/ folder. Every request must send the shared
 * secret in the X-Sync-Token header; if SYNC_TOKEN isn't configured on
 * the server, both endpoints simply 404.
 */
class FileSyncController extends Controller
{
    private const ROOT = 'barangays';

    public function manifest(Request $request): JsonResponse
    {
        $this->authorizeSync($request);

        $disk = Storage::disk('local');

        // Make sure every barangay (and its Barangay/SK subfolders)
        // exists, so new barangays show up on the laptop even before
        // any report has been filed for them.
        Barangay::all()->each(fn (Barangay $barangay) => $barangay->ensureFolder());

        $files = collect($disk->allFiles(self::ROOT))
            ->map(fn (string $path) => [
                'path' => $path,
                'size' => $disk->size($path),
                'modified' => $disk->lastModified($path),
            ])
            ->values();

        return response()->json([
            'folders' => $disk->allDirectories(self::ROOT),
            'files' => $files,
        ]);
    }

    public function file(Request $request): BinaryFileResponse
    {
        $this->authorizeSync($request);

        $path = (string) $request->query('path', '');

        abort_unless(
            str_starts_with($path, self::ROOT.'/')
                && ! str_contains($path, '..')
                && ! str_contains($path, '\\'),
            404
        );

        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path));
    }

    private function authorizeSync(Request $request): void
    {
        $expected = (string) config('services.racts_sync.token');

        abort_if($expected === '' || strlen($expected) < 32, 404);
        abort_unless(hash_equals($expected, (string) $request->header('X-Sync-Token')), 403);
    }
}
