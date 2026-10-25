<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Follow-up round: the catalog of trackable report/document "items" a
 * barangay can be logged against (client's reference table
 * `barangaylogs`). Each one has its own deadline and feeds the "Report"
 * dropdown on the Barangay Log Details list. Kept separate from
 * ReportRequirement (the grid's report slots) since this is its own
 * feature, managed from its own Barangay Logs page.
 *
 * Follow-up round: council_type scopes this catalog to the Barangay or
 * SK side of Report Filing, so the same "Add Log Entry" / "Manage
 * Reports" feature now works from either grid page's Report Filing tab,
 * each with its own independent report catalog. It reuses
 * ReportRequirement's COUNCIL_BARANGAY/COUNCIL_SK values directly
 * (rather than redeclaring its own) so the two can never drift apart.
 * Like a ReportRequirement slot's council_type, it's fixed once an item
 * exists - a report that genuinely belongs to the other council is
 * better logged as a new item there than reassigned.
 */
class BarangayLog extends Model
{
    use HasFactory;

    protected $table = 'barangay_logs';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public const FREQUENCY_ONCE = 'once';
    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_ANNUAL = 'annual';

    public const FREQUENCIES = [
        self::FREQUENCY_ONCE => 'One-time',
        self::FREQUENCY_MONTHLY => 'Monthly',
        self::FREQUENCY_ANNUAL => 'Annual',
    ];

    protected $fillable = [
        'item_description',
        'council_type',
        'frequency',
        'deadline_date',
        'deadline_day',
        'deadline_month',
        'effective_from',
        'is_mandatory',
        'status',
        'date_process',
        'processed_by',
    ];

    /**
     * Follow-up round: pinned to 'date:Y-m-d' (see BarangayLogDetail's
     * matching doc comment) so deadline_date always saves as a plain
     * 'Y-m-d' string instead of the connection's full datetime format -
     * MySQL's DATE column silently truncated the difference away, but
     * a SQLite test database doesn't, which is what broke
     * assertDatabaseHas() checks on this column in BarangayLogsTest.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline_date' => 'date:Y-m-d',
            'date_process' => 'date:Y-m-d',
            'effective_from' => 'date:Y-m-d',
            'is_mandatory' => 'boolean',
        ];
    }

    public function details()
    {
        return $this->hasMany(BarangayLogDetail::class);
    }

    public function scopeForCouncil(Builder $query, string $councilType): Builder
    {
        return $query->where('council_type', $councilType);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function councilLabel(): string
    {
        return ReportRequirement::COUNCIL_TYPES[$this->council_type] ?? $this->council_type;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isRecurring(): bool
    {
        return in_array($this->frequency, [self::FREQUENCY_MONTHLY, self::FREQUENCY_ANNUAL], true);
    }

    public function frequencyLabel(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? self::FREQUENCIES[self::FREQUENCY_ONCE];
    }

    /**
     * Deadline for one reporting period: a monthly report for "2026-10"
     * is due on deadline_day of November 2026; an annual report for
     * "2026" is due on deadline_month/deadline_day of 2027.
     */
    public function deadlineFor(string $period): Carbon
    {
        $next = $this->frequency === self::FREQUENCY_MONTHLY
            ? Carbon::createFromFormat('!Y-m', $period)->addMonthNoOverflow()
            : Carbon::create((int) $period + 1, $this->deadline_month ?? 1, 1);

        return $next->day(min($this->deadline_day ?? 5, $next->daysInMonth));
    }

    /** Human description of the deadline rule, for the admin catalog. */
    public function deadlineRuleLabel(): string
    {
        return match ($this->frequency) {
            self::FREQUENCY_MONTHLY => sprintf('Day %d of the following month', $this->deadline_day ?? 5),
            self::FREQUENCY_ANNUAL => sprintf('%s %d of the following year',
                Carbon::create(2000, $this->deadline_month ?? 1, 1)->format('F'), $this->deadline_day ?? 5),
            default => $this->deadline_date?->format('M j, Y') ?? '—',
        };
    }
}
