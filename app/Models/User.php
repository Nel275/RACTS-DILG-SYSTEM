<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';
    public const ROLE_BARANGAY = 'barangay';

    public const ROLES = [
        self::ROLE_ADMIN => 'DILG Administrator',
        self::ROLE_STAFF => 'DILG Monitoring Staff',
        self::ROLE_BARANGAY => 'Barangay User',
    ];

    /**
     * What each role may and may not do - shown on the profile page and
     * mirrored by the route middleware in routes/web.php.
     */
    public const PERMISSIONS = [
        self::ROLE_ADMIN => [
            'can' => [
                'Manage all user accounts (DILG and barangay), reset passwords, enable/disable accounts',
                'Add, edit, and deactivate barangays',
                'Manage report slots and the report catalog',
                'Record submissions, file and close reports, view analytics and exports',
                'Clear the dashboard activity feed',
            ],
            'cannot' => [
                'Delete or demote their own account, or remove the last active administrator',
            ],
        ],
        self::ROLE_STAFF => [
            'can' => [
                'View barangays, user logs, records, analytics and rankings',
                'Record submissions and file/close barangay reports',
                'Review the activity logs and receive late-report alerts',
            ],
            'cannot' => [
                'Create, edit, or delete user accounts',
                'Add, edit, or deactivate barangays',
                'Manage report slots or the report catalog',
                'Delete log entries or clear activity history',
            ],
        ],
        self::ROLE_BARANGAY => [
            'can' => [
                'View their own barangay dashboard, ranking and compliance status',
                'Upload and submit their own barangay reports',
                'Receive late-report alerts for their barangay',
                'Update their own profile and password',
            ],
            'cannot' => [
                'See or edit any other barangay data',
                'Add, edit, or delete barangays, users, or report slots',
                'Access analytics for all barangays, records, or activity logs',
            ],
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'is_active',
        'contact_number',
        'position',
        'avatar_path',
        'last_login_at',
        'barangay_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * The barangay this user is associated with, when applicable.
     * Not used by DILG Administrator/Monitoring Staff accounts today - see
     * the note in the users migration.
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * This user's compliance alerts (Phase 8).
     *
     * RACTS uses its own simple `notifications` table matching the
     * manuscript's Data Dictionary (Table 4.19) instead of Laravel's
     * built-in polymorphic notification system, so this method
     * intentionally overrides the `notifications()` relation that the
     * Notifiable trait above would otherwise provide.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest('notification_date');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isBarangay(): bool
    {
        return $this->role === self::ROLE_BARANGAY;
    }

    /**
     * DILG office personnel (administrator or monitoring staff) - the
     * accounts that see the whole system rather than one barangay.
     */
    public function isDilg(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name), -1, PREG_SPLIT_NO_EMPTY);
        $letters = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters) ?: '?';
    }

    public function hasAvatar(): bool
    {
        return $this->avatar_path !== null;
    }

    public function permissions(): array
    {
        return self::PERMISSIONS[$this->role] ?? ['can' => [], 'cannot' => []];
    }

    public function scopeActiveAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN)->where('is_active', true);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
