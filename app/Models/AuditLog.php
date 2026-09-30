<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'log_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'log_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Write one audit log entry for the given user. This is the only
     * place that should create AuditLog rows, so every entry is recorded
     * the same way.
     */
    public static function record(User $user, string $action): self
    {
        return self::create([
            'user_id' => $user->id,
            'action' => $action,
            'log_date' => now(),
        ]);
    }
}
