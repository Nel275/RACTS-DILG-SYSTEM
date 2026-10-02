<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRecord extends Model
{
    use HasFactory;

    protected $table = 'report_records';

    public const COLOR_GREEN = 'green';
    public const COLOR_YELLOW = 'yellow';
    public const COLOR_RED = 'red';

    public const COLORS = [
        self::COLOR_GREEN => 'Submitted (On Time)',
        self::COLOR_YELLOW => 'Late',
        self::COLOR_RED => 'Not Submitted',
    ];

    // Client spec (Section 2): green = 100%, yellow = 50%, red = 0%.
    // A null status_color (not graded yet) has no score - it's excluded
    // from percentage averages rather than counted as 0. See
    // RankingCalculator and REDESIGN_PHASE1_DATA_MODEL.md.
    public const SCORES = [
        self::COLOR_GREEN => 100,
        self::COLOR_YELLOW => 50,
        self::COLOR_RED => 0,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barangay_id',
        'requirement_id',
        'period',
        'submission_date',
        'status_color',
        'remarks',
        'edited_by',
        'edited_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Follow-up round: pinned to 'date:Y-m-d' instead of plain
            // 'date' - see BarangayLogDetail's matching doc comment for
            // why (MySQL's DATE column silently truncates the
            // difference away; a SQLite test database doesn't).
            'submission_date' => 'date:Y-m-d',
            'edited_at' => 'datetime',
        ];
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function reportRequirement()
    {
        return $this->belongsTo(ReportRequirement::class, 'requirement_id');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function colorLabel(): string
    {
        return self::COLORS[$this->status_color] ?? 'Not Graded';
    }

    /**
     * This record's contribution to a percentage average, or null if it
     * hasn't been graded yet (status_color is null).
     */
    public function score(): ?int
    {
        return $this->status_color ? self::SCORES[$this->status_color] : null;
    }

    /**
     * Turn a date and a report's frequency into the "period" value this
     * table stores: "YYYY-MM" for a monthly slot, "YYYY" for a yearly
     * one. Used whenever a grid cell is recorded or edited.
     */
    public static function periodFor(\DateTimeInterface $date, string $frequency): string
    {
        return $frequency === ReportRequirement::FREQUENCY_YEARLY
            ? $date->format('Y')
            : $date->format('Y-m');
    }
}
