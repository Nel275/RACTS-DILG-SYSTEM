<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * One digital stamp applied by a DILG Administrator to a report entry's
 * file (see App\Services\ReportStamper). Rows are write-once: a stamp is
 * history, so it can never be edited after it is created.
 */
class ReportStamp extends Model
{
    protected $fillable = [
        'barangay_log_detail_id',
        'user_id',
        'stamped_by_name',
        'verification_code',
        'original_file_hash',
        'stamped_file_path',
        'stamped_original_name',
        'stamped_file_hash',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Stamp records cannot be modified.'));
    }

    public function barangayLogDetail()
    {
        return $this->belongsTo(BarangayLogDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * True while this stamp covers the file the report currently carries;
     * false once the report has been resubmitted with a different file.
     */
    public function isCurrent(): bool
    {
        return $this->original_file_hash === $this->barangayLogDetail->report_file_hash;
    }
}
