<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Follow-up round: a barangay's personnel/officials roster (client's
 * reference table `userslogs`). Deliberately NOT related to the real
 * `users` table - a UserLog entry is a personnel record (name,
 * birthdate, designation, role), not a login account, and creating one
 * here never creates or touches an actual RACTS user account.
 */
class UserLog extends Model
{
    use HasFactory;

    protected $table = 'user_logs';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'gender',
        'designation',
        'roles',
        'status',
        'date_process',
        'processed_by',
        'barangay_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'date_process' => 'date',
        ];
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function fullName(): string
    {
        return trim(sprintf(
            '%s %s %s',
            $this->first_name,
            $this->middle_name ? $this->middle_name.' ' : '',
            $this->last_name
        ));
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
