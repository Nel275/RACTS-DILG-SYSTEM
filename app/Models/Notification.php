<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * "Clear Notifications" (NotificationController::clearAll()) soft-
     * deletes rather than hard-deletes, specifically so
     * CheckLateSubmissions's dedup check can still see - via
     * withTrashed() - that this user was already notified about a given
     * still-open problem, and won't recreate it. Soft-deleted rows are
     * still excluded from every normal query (the notification list,
     * the unread badge), so from the user's point of view it's gone
     * either way.
     */

    /**
     * Follow-up Round 5's four automated event types. Older rows (from
     * before this round) have type = null - they still display fine
     * (the view falls back to a generic look for a null type), they
     * just predate this categorization.
     */
    public const TYPE_PERIOD_OPEN = 'period_open';

    public const TYPE_DEADLINE_TODAY = 'deadline_today';

    public const TYPE_REPORT_LATE = 'report_late';

    public const TYPE_LATE_SUBMISSION = 'late_submission';

    // My Reports (Report Filing) events - keyed by barangay_log_detail_id.
    public const TYPE_REPORT_ASSIGNED = 'report_assigned';

    public const TYPE_REPORT_DUE_SOON = 'report_due_soon';

    public const TYPE_REPORT_OVERDUE = 'report_overdue';

    public const TYPE_REPORT_RECEIVED = 'report_received';

    public const TYPE_REPORT_RETURNED = 'report_returned';

    public const TYPE_REQUIREMENT_CHANGED = 'requirement_changed';

    public const TYPES = [
        self::TYPE_REPORT_ASSIGNED => 'New Report Required',
        self::TYPE_REPORT_DUE_SOON => 'Report Due Soon',
        self::TYPE_REPORT_OVERDUE => 'Report Overdue',
        self::TYPE_REPORT_RECEIVED => 'Report Received',
        self::TYPE_REPORT_RETURNED => 'Report Returned for Revision',
        self::TYPE_REQUIREMENT_CHANGED => 'Report Requirement Changed',
        self::TYPE_PERIOD_OPEN => 'Submission Period Open',
        self::TYPE_DEADLINE_TODAY => 'Submission Deadline Today',
        self::TYPE_REPORT_LATE => 'Late Report Submission',
        self::TYPE_LATE_SUBMISSION => 'Late Submission',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'report_record_id',
        'type',
        'barangay_id',
        'requirement_id',
        'period',
        'barangay_log_detail_id',
        'message',
        'notification_date',
        'is_read',
        'auto_generated',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notification_date' => 'datetime',
            'is_read' => 'boolean',
            'auto_generated' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The specific submission this alert is about, when one exists yet
     * (a report that was never recorded at all still generates an
     * alert, but has no report_records row to link to).
     */
    public function reportRecord()
    {
        return $this->belongsTo(ReportRecord::class);
    }

    /**
     * The barangay this alert is about - stored directly (Round 5)
     * rather than only reachable through reportRecord, since a
     * "never submitted at all" alert has no report_records row to go
     * through.
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * The report slot this alert is about - same reasoning as
     * barangay() above.
     */
    public function reportRequirement()
    {
        return $this->belongsTo(ReportRequirement::class, 'requirement_id');
    }

    /**
     * This event type's deadline for display purposes - null for a
     * notification predating Round 5 (no period/type stored) or one
     * with no deadline concept.
     */
    public function deadline(): ?\Illuminate\Support\Carbon
    {
        if (! $this->period || ! $this->reportRequirement) {
            return null;
        }

        return app(\App\Services\ReportDeadlineService::class)
            ->deadlineFor($this->period, $this->reportRequirement->frequency);
    }

    /**
     * Notifies the active barangay accounts of one barangay (or of every
     * barangay when $barangayId is null). With $once, users already sent
     * this type for this entry - even if they cleared it (withTrashed) -
     * are skipped, so repeated scheduler runs never duplicate a reminder.
     */
    public static function notifyBarangay(?int $barangayId, string $type, string $message, ?BarangayLogDetail $entry = null, bool $once = false): int
    {
        $users = User::where('role', User::ROLE_BARANGAY)
            ->where('is_active', true)
            ->when($barangayId, fn ($q) => $q->where('barangay_id', $barangayId))
            ->when($once && $entry, fn ($q) => $q->whereNotIn('id', self::withTrashed()
                ->where('type', $type)->where('barangay_log_detail_id', $entry->id)->select('user_id')))
            ->get();

        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id,
                'type' => $type,
                'barangay_id' => $user->barangay_id,
                'barangay_log_detail_id' => $entry?->id,
                'period' => $entry?->period,
                'message' => $message,
                'notification_date' => now(),
                'is_read' => false,
                'auto_generated' => true,
            ]);
        }

        return $users->count();
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? 'Alert';
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeAutoGenerated(Builder $query): Builder
    {
        return $query->where('auto_generated', true);
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true]);
        }
    }
}
