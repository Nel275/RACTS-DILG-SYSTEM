<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Follow-up round: the catalog of trackable report/document "items" a
 * barangay can be logged against (client's reference table
 * `barangaylogs`). Each one has its own deadline and feeds the "Report"
 * dropdown on the Barangay Log Details list. Kept separate from
 * ReportRequirement (the grid's report slots) since this is its own
 * feature, managed from its own Barangay Logs page.
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
        'deadline_date',
        'status',
        'date_process',
        'processed_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
            'date_process' => 'date',
        ];
    }

    public function details()
    {
        return $this->hasMany(BarangayLogDetail::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
