<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Follow-up round: one entry per barangay per logged item (client's
 * reference table `barangaylogsdetails`) - the data behind the new
 * Barangay Log Details list (built from the "Purchases" screenshot
 * reference: Number -> barangay, Supplier -> Report, with the
 * Receiving Store/Total/Balance columns left out as asked).
 *
 * `status` is stored (not just computed) so it can be filtered on
 * directly, but it's kept in sync automatically rather than requiring
 * a manual step: BarangayLogDetailController reconciles any entry
 * whose deadline has passed with nothing submitted to "missed" every
 * time the list is loaded (see its index() method), and
 * markSubmitted() below decides "submitted" vs "late" the moment a
 * submission is recorded.
 */
class BarangayLogDetail extends Model
{
    use HasFactory;

    protected $table = 'barangay_log_details';

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_LATE = 'late';
    public const STATUS_MISSED = 'missed';
    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_SUBMITTED => 'Submitted',
        self::STATUS_LATE => 'Submitted Late',
        self::STATUS_MISSED => 'Missed',
        self::STATUS_CLOSED => 'Closed',
    ];

    /**
     * Maps a status to one of the existing racts-badge-* classes
     * (public/css/racts.css) already used for the grid's Timeliness
     * column, so this new list reads consistently with the rest of the
     * app rather than inventing a second color language. Closed uses
     * plain Bootstrap dark-subtle rather than any of the three
     * green/yellow/red compliance colors, since "closed" isn't itself a
     * compliance judgement - it's just "done, filed away."
     */
    public const BADGE_CLASSES = [
        self::STATUS_PENDING => 'bg-secondary-subtle text-secondary-emphasis',
        self::STATUS_SUBMITTED => 'racts-badge-green',
        self::STATUS_LATE => 'racts-badge-yellow',
        self::STATUS_MISSED => 'racts-badge-red',
        self::STATUS_CLOSED => 'bg-dark-subtle text-dark-emphasis',
    ];

    protected $fillable = [
        'barangay_log_id',
        'barangay_id',
        'item_description',
        'deadline_date',
        'date_submitted',
        'status',
        'date_process',
        'processed_by',
        'report_file_path',
        'report_original_name',
        'report_filed_at',
    ];

    /**
     * Follow-up round: 'date' (with no explicit format) saves using the
     * connection's full datetime format ('Y-m-d H:i:s'), not just
     * 'Y-m-d' - MySQL's DATE column type quietly truncates the time
     * back off on insert, so this never showed up against it, but a
     * SQLite test database has no such column-type enforcement and
     * stores the string exactly as given, which broke
     * assertDatabaseHas() checks on deadline_date in BarangayLogsTest.
     * 'date:Y-m-d' pins the storage format explicitly so it's
     * consistent on every database, not just ones that happen to paper
     * over it.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline_date' => 'date:Y-m-d',
            'date_submitted' => 'datetime',
            'date_process' => 'date:Y-m-d',
            'report_filed_at' => 'datetime',
        ];
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function barangayLog()
    {
        return $this->belongsTo(BarangayLog::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function badgeClass(): string
    {
        return self::BADGE_CLASSES[$this->status] ?? 'bg-secondary-subtle text-secondary-emphasis';
    }

    /**
     * Records a submission and decides submitted-vs-late from the
     * deadline right here, so the caller never has to work that logic
     * out itself. $reportFilePath/$reportOriginalName are the staged
     * upload from the Mark Submitted form, if one was attached -
     * "staged" because it isn't filed into the barangay's folder until
     * fileInto() (the Download action) runs.
     */
    public function markSubmitted(
        \DateTimeInterface $submittedAt,
        string $processedBy,
        ?string $reportFilePath = null,
        ?string $reportOriginalName = null
    ): void {
        $isLate = $submittedAt > $this->deadline_date->copy()->endOfDay();

        $attributes = [
            'date_submitted' => $submittedAt,
            'status' => $isLate ? self::STATUS_LATE : self::STATUS_SUBMITTED,
            'date_process' => now(),
            'processed_by' => $processedBy,
        ];

        if ($reportFilePath !== null) {
            $attributes['report_file_path'] = $reportFilePath;
            $attributes['report_original_name'] = $reportOriginalName;
            // A freshly (re-)uploaded file replaces whatever was staged
            // or filed before, so it needs to be filed again.
            $attributes['report_filed_at'] = null;
        }

        $this->update($attributes);
    }

    public function hasReportFile(): bool
    {
        return ! is_null($this->report_file_path);
    }

    public function isFiled(): bool
    {
        return ! is_null($this->report_filed_at);
    }

    /**
     * Moves the staged report file into this entry's barangay's folder
     * (the "Download" action - see BarangayLogDetailController::download()).
     * Renamed on the way in (id + original name) so two entries' files
     * can never collide inside the same barangay folder.
     */
    public function fileIntoBarangayFolder(string $processedBy): void
    {
        $this->barangay->ensureFolder();

        $safeName = $this->id.'-'.($this->report_original_name ?? basename($this->report_file_path));
        $destination = $this->barangay->storagePath().'/'.$this->councilFolder().'/'.$safeName;

        Storage::disk('local')->move($this->report_file_path, $destination);

        $this->update([
            'report_file_path' => $destination,
            'report_filed_at' => now(),
            'date_process' => now(),
            'processed_by' => $processedBy,
        ]);
    }

    /**
     * "Barangay" or "SK" - which council this entry's report belongs to,
     * used as the subfolder name for its file.
     */
    public function councilFolder(): string
    {
        return ReportRequirement::COUNCIL_TYPES[$this->barangayLog?->council_type ?? ReportRequirement::COUNCIL_BARANGAY]
            ?? ReportRequirement::COUNCIL_TYPES[ReportRequirement::COUNCIL_BARANGAY];
    }

    /**
     * Staging folder for this entry's upload, named after the council and
     * barangay (e.g. incoming-reports/SK/Centro West) instead of the entry id.
     */
    public function incomingFolder(): string
    {
        return Barangay::incomingPath($this->councilFolder(), $this->barangay->folderName());
    }

    public function close(string $processedBy): void
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
            'date_process' => now(),
            'processed_by' => $processedBy,
        ]);
    }
}
