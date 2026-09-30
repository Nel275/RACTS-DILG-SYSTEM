<?php

namespace App\Services;

use App\Models\ReportRequirement;
use Illuminate\Support\Carbon;

/**
 * Follow-up Round 5: the DILG submission-window rule (client spec
 * Section 4 - due the 1st-5th of the month after the report's period)
 * lived only inside CheckLateSubmissions before this round, computed
 * inline and only reachable from there. Pulling it out here means the
 * exact same rule can be reused by both the scheduled overdue check
 * AND the real-time late-submission check in ReportGridService,
 * without duplicating the month-rollover math in two places.
 *
 * Deliberately reads nothing from ReportRecord::status_color and writes
 * nothing back to the database - this is a pure calculator over
 * (period, frequency, an optional submission date, "today"). That
 * keeps it fully independent of the existing report-status system, per
 * the client's own instruction not to redesign that system just to
 * support notifications.
 */
class ReportDeadlineService
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ON_TIME = 'on_time';

    public const STATUS_LATE = 'late';

    public const STATUS_OVERDUE = 'overdue';

    /**
     * Yearly report slots have no DILG lateness rule (client spec
     * Section 6, decision #4) - the original Phase 6 automation
     * excluded them entirely, and this round keeps that unchanged.
     */
    public const STATUS_NOT_APPLICABLE = 'not_applicable';

    public function windowStartDay(): int
    {
        return (int) config('racts.submission_window_start_day', 1);
    }

    public function deadlineDay(): int
    {
        return (int) config('racts.submission_deadline_day', 5);
    }

    /**
     * The day the submission window opens for a monthly report's
     * $period ("2026-08") - the 1st of the following month, at
     * 00:00:00. Null for a yearly slot.
     */
    public function windowStartFor(string $period, string $frequency): ?Carbon
    {
        if ($frequency !== ReportRequirement::FREQUENCY_MONTHLY) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $period.'-01')
            ->addMonthNoOverflow()
            ->startOfMonth()
            ->addDays($this->windowStartDay() - 1)
            ->startOfDay();
    }

    /**
     * The last moment a monthly report for $period can be submitted on
     * time - end of day on deadlineDay() of the month after $period.
     * Handles year rollover (December's report is due in January)
     * automatically via addMonthNoOverflow(). Null for a yearly slot.
     */
    public function deadlineFor(string $period, string $frequency): ?Carbon
    {
        if ($frequency !== ReportRequirement::FREQUENCY_MONTHLY) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $period.'-01')
            ->addMonthNoOverflow()
            ->startOfMonth()
            ->addDays($this->deadlineDay() - 1)
            ->endOfDay();
    }

    /**
     * on_time / late / overdue / pending / not_applicable - the single
     * place this decision is made, so the scheduled checker and the
     * real-time submission check can never disagree with each other.
     *
     * RACTS records a submission DATE, not a timestamp (the grid is a
     * date picker, not a date+time field), so "submitted on the 5th"
     * and "submitted at 11:59 PM on the 5th" are the same value here -
     * the day-level comparison below (on/before the deadline day, or
     * after it) is what's actually meaningful given how staff enter
     * this data.
     */
    public function statusFor(
        string $period,
        string $frequency,
        ?\DateTimeInterface $submissionDate,
        ?\DateTimeInterface $today = null
    ): string {
        $deadline = $this->deadlineFor($period, $frequency);

        if ($deadline === null) {
            return self::STATUS_NOT_APPLICABLE;
        }

        if ($submissionDate !== null) {
            return Carbon::instance($submissionDate)->lte($deadline)
                ? self::STATUS_ON_TIME
                : self::STATUS_LATE;
        }

        $today = $today ? Carbon::instance($today) : Carbon::now();

        return $today->gt($deadline) ? self::STATUS_OVERDUE : self::STATUS_PENDING;
    }

    /**
     * Human-readable label for statusFor()'s return value - used by the
     * Records page badge and anywhere else that needs to show this to
     * a person instead of branching on the raw string.
     */
    public function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_ON_TIME => 'On Time',
            self::STATUS_LATE => 'Late',
            self::STATUS_OVERDUE => 'Overdue',
            self::STATUS_PENDING => 'Pending',
            default => 'N/A',
        };
    }
}
