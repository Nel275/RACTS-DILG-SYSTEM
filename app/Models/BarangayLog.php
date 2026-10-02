<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $fillable = [
        'item_description',
        'council_type',
        'deadline_date',
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
}
