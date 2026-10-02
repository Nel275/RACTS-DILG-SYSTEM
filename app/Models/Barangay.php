<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Barangay extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barangay_name',
        'status',
        'date_process',
        'processed_by',
    ];

    /**
     * Follow-up round: pinned to 'date:Y-m-d' instead of plain 'date' -
     * see BarangayLogDetail's matching doc comment for why (MySQL's
     * DATE column silently truncates the difference away; a SQLite
     * test database doesn't).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_process' => 'date:Y-m-d',
        ];
    }

    /**
     * Users (DILG personnel) associated with this barangay, when applicable.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Submission history recorded for this barangay.
     */
    public function reportRecords()
    {
        return $this->hasMany(ReportRecord::class);
    }

    /**
     * Follow-up round: entries on the new Barangay Log Details list
     * (barangay_log_details) for this barangay.
     */
    public function logDetails()
    {
        return $this->hasMany(BarangayLogDetail::class);
    }

    /**
     * Follow-up round: this barangay's personnel roster (user_logs) -
     * unrelated to real login accounts, see UserLog's own doc comment.
     */
    public function personnel()
    {
        return $this->hasMany(UserLog::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Follow-up round: where this barangay's filed reports live on disk
     * (relative to storage/app, the Laravel 'local' disk's root).
     *
     * Previously keyed by id (barangays/5) so a rename could never
     * orphan already-filed reports. Now named after the barangay
     * itself (barangays/Malvar) per follow-up request, so the folder
     * reads clearly on disk without having to cross-reference an id.
     * The orphaning risk that id-keying protected against is instead
     * handled by moveFolderForRename() below, called from
     * BarangayController::update() whenever barangay_name changes -
     * the folder (and every already-filed entry's stored file path)
     * moves with the rename instead of being left behind. Collisions
     * between two different barangays are prevented by barangay_name
     * being required unique (see BarangayController::validateBarangay()).
     */
    public function storagePath(): string
    {
        return 'barangays/'.$this->folderName();
    }

    /**
     * The barangay's name, sanitized into a name that's safe to use as
     * a folder on disk. Falls back to "barangay-{id}" only in the
     * unlikely case the name is nothing but characters that get
     * stripped out (e.g. a name of pure punctuation).
     */
    public function folderName(): string
    {
        return static::sanitizeFolderName($this->barangay_name) ?: ('barangay-'.$this->id);
    }

    /**
     * Strips characters that are illegal in a Windows folder name (this
     * app runs on XAMPP/Windows) - also unsafe cross-platform - trims
     * doubled-up whitespace, and drops a trailing dot or space (which
     * Windows silently disallows too).
     */
    public static function sanitizeFolderName(string $name): string
    {
        $clean = preg_replace('/[<>:"\/\\\\|?*\x00-\x1F]/', '', $name) ?? '';
        $clean = trim(preg_replace('/\s+/', ' ', $clean) ?? '');

        return rtrim($clean, ' .');
    }

    /**
     * Creates this barangay's folder if it doesn't already exist.
     * Called when the barangay is added, and defensively again right
     * before a report is filed into it (covers a barangay created
     * before this feature existed).
     *
     * Follow-up round: also transparently migrates a folder still
     * sitting under the old id-based path (barangays/{id}, from before
     * this round) to the new name-based path the first time it's
     * touched after upgrading - so barangays created before this round
     * don't lose reports already filed under the old scheme.
     */
    public function ensureFolder(): void
    {
        $target = $this->storagePath();
        $legacy = 'barangays/'.$this->id;

        if ($legacy !== $target
            && Storage::disk('local')->exists($legacy)
            && ! Storage::disk('local')->exists($target)
        ) {
            $this->moveFolder($legacy, $target);
        }

        Storage::disk('local')->makeDirectory($target);
        $this->ensureCouncilFolders();
    }

    /**
     * Creates the per-council subfolders for this barangay, so every
     * barangay gets named folders for both Barangay and SK reports:
     *   barangays/{Name}/Barangay, barangays/{Name}/SK      (filed)
     *   incoming-reports/Barangay/{Name}, incoming-reports/SK/{Name} (uploaded, not yet filed)
     */
    public function ensureCouncilFolders(): void
    {
        $disk = Storage::disk('local');

        foreach (ReportRequirement::COUNCIL_TYPES as $councilLabel) {
            $disk->makeDirectory($this->storagePath().'/'.$councilLabel);
            $disk->makeDirectory(static::incomingPath($councilLabel, $this->folderName()));
        }
    }

    /**
     * Where uploaded-but-not-yet-filed reports are staged for a council
     * and barangay, e.g. incoming-reports/SK/Centro West.
     */
    public static function incomingPath(string $councilLabel, string $folderName): string
    {
        return 'incoming-reports/'.$councilLabel.'/'.$folderName;
    }

    /**
     * Moves this barangay's folder (and rewrites every already-filed
     * log entry's stored file path to match) after barangay_name has
     * changed. Called from BarangayController::update(). $previousName
     * is the name the barangay had a moment ago, before the update
     * that's already been applied to this model instance.
     */
    public function moveFolderForRename(string $previousName): void
    {
        $oldFolderName = static::sanitizeFolderName($previousName) ?: ('barangay-'.$this->id);
        $this->moveIncomingFoldersForRename($oldFolderName);

        $oldPath = 'barangays/'.$oldFolderName;
        $newPath = $this->storagePath();

        if ($oldPath === $newPath) {
            return;
        }

        if (Storage::disk('local')->exists($oldPath)) {
            $this->moveFolder($oldPath, $newPath);
        } else {
            $this->ensureFolder();
        }

        // The folder above just moved on disk - every entry already
        // filed under the old path needs its stored path updated to
        // match, or it'd point at a location that no longer exists.
        $this->logDetails()
            ->where('report_file_path', 'like', $oldPath.'/%')
            ->get()
            ->each(function (BarangayLogDetail $entry) use ($oldPath, $newPath) {
                $entry->update([
                    'report_file_path' => Str::replaceFirst($oldPath.'/', $newPath.'/', $entry->report_file_path),
                ]);
            });
    }

    /**
     * Renames this barangay's staging folders (incoming-reports/{Council}/{Name})
     * after a rename and rewrites the stored path of every report still
     * staged there, so uploads that haven't been filed yet keep working.
     */
    private function moveIncomingFoldersForRename(string $oldFolderName): void
    {
        foreach (ReportRequirement::COUNCIL_TYPES as $councilLabel) {
            $oldPath = static::incomingPath($councilLabel, $oldFolderName);
            $newPath = static::incomingPath($councilLabel, $this->folderName());

            if ($oldPath === $newPath) {
                continue;
            }

            if (Storage::disk('local')->exists($oldPath)) {
                $this->moveFolder($oldPath, $newPath);
            }

            $this->logDetails()
                ->where('report_file_path', 'like', $oldPath.'/%')
                ->get()
                ->each(function (BarangayLogDetail $entry) use ($oldPath, $newPath) {
                    $entry->update([
                        'report_file_path' => Str::replaceFirst($oldPath.'/', $newPath.'/', $entry->report_file_path),
                    ]);
                });
        }
    }

    /**
     * Actually moves a folder on disk. Storage::move() (Flysystem) is
     * documented for files, not directories - PHP's native rename()
     * against the disk's real absolute paths is used instead, which
     * correctly moves a whole directory tree on a local filesystem
     * (this app always uses the 'local' disk).
     */
    private function moveFolder(string $from, string $to): void
    {
        $disk = Storage::disk('local');
        $fromAbsolute = $disk->path($from);
        $toAbsolute = $disk->path($to);

        if (! is_dir(dirname($toAbsolute))) {
            mkdir(dirname($toAbsolute), 0755, true);
        }

        if (is_dir($fromAbsolute) && ! is_dir($toAbsolute)) {
            @rename($fromAbsolute, $toAbsolute);
        }

        // Whichever path was taken above, make sure the target exists -
        // a defensive fallback rather than leaving this barangay with
        // no folder at all if something odd happened on disk.
        if (! is_dir($toAbsolute)) {
            $disk->makeDirectory($to);
        }
    }
}
