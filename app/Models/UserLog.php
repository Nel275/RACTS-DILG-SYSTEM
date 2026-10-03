<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Follow-up round: a barangay's personnel/officials roster (client's
 * reference table `userslogs`). A UserLog entry is a personnel record
 * (name, birthdate, designation, role) first and foremost - most never
 * touch the real `users` table at all. Optionally, though, a personnel
 * record can be given a real RACTS login account (DILG Administrator or
 * DILG Monitoring Staff role only): `user_id` links to that account when
 * one exists, and stays null for a plain personnel record with no login
 * access. See `account()` / `hasLoginAccount()` below.
 */
class UserLog extends Model
{
    use HasFactory;

    protected $table = 'user_logs';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /** Options for the Gender field on the personnel form. */

    public const GENDERS = ['Male', 'Female'];


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
        'user_id',
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
            'birthdate' => 'date:Y-m-d',
            'date_process' => 'date:Y-m-d',
        ];
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * The real RACTS login account linked to this personnel record, if
     * one has been given to them. Null means they have no login access.
     */
    public function account()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hasLoginAccount(): bool
    {
        return $this->user_id !== null;
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
