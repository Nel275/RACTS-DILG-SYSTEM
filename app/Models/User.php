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

    public const ROLES = [
        self::ROLE_ADMIN => 'DILG Administrator',
        self::ROLE_STAFF => 'DILG Monitoring Staff',
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

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
