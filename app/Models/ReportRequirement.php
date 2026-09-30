<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRequirement extends Model
{
    use HasFactory;

    protected $table = 'report_requirements';

    public const COUNCIL_BARANGAY = 'barangay';
    public const COUNCIL_SK = 'sk';

    public const COUNCIL_TYPES = [
        self::COUNCIL_BARANGAY => 'Barangay',
        self::COUNCIL_SK => 'SK',
    ];

    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_YEARLY = 'yearly';

    public const FREQUENCIES = [
        self::FREQUENCY_MONTHLY => 'Monthly',
        self::FREQUENCY_YEARLY => 'Yearly',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'report_name',
        'council_type',
        'frequency',
        'display_order',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Submission records recorded against this report slot.
     */
    public function reportRecords()
    {
        return $this->hasMany(ReportRecord::class, 'requirement_id');
    }

    public function scopeForCouncil(Builder $query, string $councilType): Builder
    {
        return $query->where('council_type', $councilType);
    }

    public function scopeMonthly(Builder $query): Builder
    {
        return $query->where('frequency', self::FREQUENCY_MONTHLY);
    }

    public function scopeYearly(Builder $query): Builder
    {
        return $query->where('frequency', self::FREQUENCY_YEARLY);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isMonthly(): bool
    {
        return $this->frequency === self::FREQUENCY_MONTHLY;
    }

    public function isYearly(): bool
    {
        return $this->frequency === self::FREQUENCY_YEARLY;
    }

    public function councilLabel(): string
    {
        return self::COUNCIL_TYPES[$this->council_type] ?? $this->council_type;
    }

    public function frequencyLabel(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? $this->frequency;
    }
}
