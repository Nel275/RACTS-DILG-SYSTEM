<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\Notification;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Models\User;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Shared logic behind the Barangay Reports and SK Reports grid pages
 * (client spec Sections 3-4). Both pages are the exact same grid - rows
 * are barangays, columns are that council's active report slots - so
 * this service is written once against a $councilType instead of having
 * two near-identical copies of the same code, one per page.
 *
 * Judgment call worth knowing for the defense write-up (documented the
 * same way as Phase 1's null-status-color call, in
 * REDESIGN_PHASE2_REPORT_GRIDS.md): the reference PPTX shows one page
 * per council with a single month/year visible at a time, plus the
 * spec's separate Start/End Month filter. This implementation reads
 * that as two independent controls - "period month" below is the ONE
 * period being viewed and edited (it decides which columns' cells are
 * shown), while the Start/End Month range (handled by RankingCalculator,
 * not here) only ever changes the percentage/ranking shown alongside
 * it. Changing the range never changes which cells you're editing, and
 * changing the period being edited never changes the percentage.
 */
class ReportGridService
{
    /**
     * The active report slots for one council, ordered the way they
     * appear left-to-right in the grid: monthly slots first (by
     * display_order, then name), then yearly slots - matching the
     * reference layout, where the annual column sits after the monthly
     * ones.
     */
    public function columnsFor(string $councilType): Collection
    {
        return ReportRequirement::forCouncil($councilType)
            ->active()
            ->orderByRaw("frequency = '".ReportRequirement::FREQUENCY_YEARLY."'")
            ->orderBy('display_order')
            ->orderBy('report_name')
            ->get();
    }

    /**
     * Every report slot for one council, active or not - used by the
     * admin slot-management panel, which needs to show (and let the
     * admin reactivate) deactivated slots too.
     */
    public function allSlotsFor(string $councilType): Collection
    {
        return ReportRequirement::forCouncil($councilType)
            ->orderByRaw("frequency = '".ReportRequirement::FREQUENCY_YEARLY."'")
            ->orderBy('display_order')
            ->orderBy('report_name')
            ->get();
    }

    /**
     * Every active barangay, alphabetically - the grid's rows.
     */
    public function barangaysFor(): Collection
    {
        return Barangay::where('status', Barangay::STATUS_ACTIVE)
            ->orderBy('barangay_name')
            ->get();
    }

    /**
     * The period string ("2026-09" for monthly, "2026" for yearly) a
     * report slot's cell uses for the currently selected period month.
     */
    public function periodFor(ReportRequirement $requirement, string $periodMonth): string
    {
        return ReportRecord::periodFor(
            DateTime::createFromFormat('Y-m-d', $periodMonth.'-01'),
            $requirement->frequency
        );
    }

    /**
     * Existing ReportRecord rows for the given columns/period, keyed
     * [barangay_id][requirement_id] so the grid view can look a cell up
     * directly instead of searching a flat list for every one of it.
     *
     * @return array<int, array<int, ReportRecord>>
     */
    public function cellsFor(Collection $columns, string $periodMonth): array
    {
        $periodsByRequirement = $columns->mapWithKeys(
            fn (ReportRequirement $r) => [$r->id => $this->periodFor($r, $periodMonth)]
        );

        $records = ReportRecord::whereIn('requirement_id', $columns->pluck('id'))
            ->get()
            ->filter(fn (ReportRecord $r) => ($periodsByRequirement[$r->requirement_id] ?? null) === $r->period);

        $cells = [];
        foreach ($records as $record) {
            $cells[$record->barangay_id][$record->requirement_id] = $record;
        }

        return $cells;
    }

    /**
     * Save one barangay's row of cells in a single call - one <form>
     * per row on the grid page, matching RACTS's no-AJAX, full-page-
     * reload convention. $cellsInput is the raw `cells` array from the
     * request: [requirement_id => ['date' => ..., 'color' => ...]].
     *
     * submission_date is required by the database - it's what makes a
     * cell "recorded" at all - so clearing a cell's date deletes any
     * existing record for it rather than leaving an invalid row behind.
     * A date with no color is a valid, meaningful state: recorded, not
     * graded yet (status_color stays null - see ReportRecord::score()
     * and RankingCalculator).
     */
    public function saveRow(
        int $barangayId,
        Collection $columns,
        string $periodMonth,
        array $cellsInput,
        int $editedByUserId
    ): void {
        // Loaded once per save rather than once per cell - a row can
        // touch several columns, and the audit trail below (see the
        // AuditLog::record() calls) wants the human-readable barangay
        // name and the editor's own record for its messages.
        $barangayName = Barangay::find($barangayId)?->barangay_name ?? 'Unknown Barangay';
        $editor = User::find($editedByUserId);

        foreach ($columns as $requirement) {
            $input = $cellsInput[$requirement->id] ?? [];
            $date = trim((string) ($input['date'] ?? ''));
            $color = trim((string) ($input['color'] ?? ''));
            $period = $this->periodFor($requirement, $periodMonth);
            $periodLabel = $this->periodLabel($period, $requirement->frequency);

            $existing = ReportRecord::where('barangay_id', $barangayId)
                ->where('requirement_id', $requirement->id)
                ->where('period', $period)
                ->first();

            if ($date === '') {
                if ($existing) {
                    $existing->delete();

                    if ($editor) {
                        AuditLog::record($editor, sprintf(
                            'Cleared "%s" for %s — %s.',
                            $requirement->report_name,
                            $barangayName,
                            $periodLabel
                        ));
                    }
                }

                continue;
            }

            $values = [
                'submission_date' => $date,
                'status_color' => $color !== '' ? $color : null,
                'edited_by' => $editedByUserId,
                'edited_at' => now(),
            ];

            $wasExisting = (bool) $existing;

            if ($existing) {
                $existing->update($values);
                $record = $existing;
            } else {
                $record = ReportRecord::create($values + [
                    'barangay_id' => $barangayId,
                    'requirement_id' => $requirement->id,
                    'period' => $period,
                ]);
            }

            // Follow-up Round 5, Step 3.1: the real-time half of the
            // automatic late-submission notification feature - checked
            // synchronously, right here where every submission date
            // actually gets saved, so a late report is flagged the
            // moment it's entered rather than waiting for the next
            // scheduled check. See ReportDeadlineService for the rule
            // and CheckLateSubmissions for the scheduled backstop that
            // covers a report that's never submitted at all.
            $this->notifyIfLate($record, $requirement);

            if ($editor) {
                $gradeLabel = $color !== '' ? (ReportRecord::COLORS[$color] ?? $color) : 'not yet graded';

                AuditLog::record($editor, sprintf(
                    '%s "%s" for %s — %s (%s).',
                    $wasExisting ? 'Updated' : 'Recorded',
                    $requirement->report_name,
                    $barangayName,
                    $periodLabel,
                    $gradeLabel
                ));
            }
        }
    }

    /**
     * "2026-09" -> "September 2026" for a monthly slot's period; a
     * yearly slot's period ("2026") is already the label. Used both for
     * the audit-log messages above and for the late-submission
     * notification text below - the grid itself still works with the
     * raw period string everywhere else.
     */
    private function periodLabel(string $period, string $frequency): string
    {
        if ($frequency === ReportRequirement::FREQUENCY_YEARLY) {
            return $period;
        }

        return DateTime::createFromFormat('Y-m-d', $period.'-01')->format('F Y');
    }

    /**
     * Follow-up Round 5, Step 3.1 - the real-time layer of the
     * automatic late-submission notification feature. Runs on every
     * save; a no-op unless ReportDeadlineService (the single place the
     * DILG 1st-5th rule is calculated - see that class) says this
     * exact submission date is late for this period. Synchronous, not
     * queued - the check is a couple of lightweight queries, and
     * nothing on this project runs a queue worker, so a queued job
     * here would silently never execute.
     *
     * Uses the same (type, barangay_id, requirement_id, period)
     * structured dedup key as CheckLateSubmissions, so editing the same
     * cell's date twice (or the scheduled command's safety-net check
     * later catching the same record) never creates a second alert for
     * a user who already has one.
     */
    private function notifyIfLate(ReportRecord $record, ReportRequirement $requirement): void
    {
        $deadlines = app(ReportDeadlineService::class);

        $status = $deadlines->statusFor($record->period, $requirement->frequency, $record->submission_date);

        if ($status !== ReportDeadlineService::STATUS_LATE) {
            return;
        }

        $deadline = $deadlines->deadlineFor($record->period, $requirement->frequency);
        $barangay = Barangay::find($record->barangay_id);
        $periodLabel = $this->periodLabel($record->period, $requirement->frequency);

        $message = sprintf(
            'Late Submission: %s (%s Council) submitted "%s" for %s on %s, after the %s deadline.',
            $barangay?->barangay_name ?? 'Unknown Barangay',
            $requirement->councilLabel(),
            $requirement->report_name,
            $periodLabel,
            $record->submission_date->format('F j, Y'),
            $deadline?->format('F j, Y') ?? 'the'
        );

        $alreadyNotifiedUserIds = Notification::withTrashed()
            ->where('auto_generated', true)
            ->where('type', Notification::TYPE_LATE_SUBMISSION)
            ->where('barangay_id', $record->barangay_id)
            ->where('requirement_id', $requirement->id)
            ->where('period', $record->period)
            ->pluck('user_id')
            ->all();

        foreach (User::all() as $user) {
            if (in_array($user->id, $alreadyNotifiedUserIds, true)) {
                continue;
            }

            Notification::create([
                'user_id' => $user->id,
                'report_record_id' => $record->id,
                'type' => Notification::TYPE_LATE_SUBMISSION,
                'barangay_id' => $record->barangay_id,
                'requirement_id' => $requirement->id,
                'period' => $record->period,
                'message' => $message,
                'notification_date' => now(),
                'is_read' => false,
                'auto_generated' => true,
            ]);
        }
    }
}
