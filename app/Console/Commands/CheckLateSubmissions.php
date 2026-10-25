<?php

namespace App\Console\Commands;

use App\Models\Barangay;
use App\Models\Notification;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Models\User;
use App\Services\ReportDeadlineService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Client spec Section 4 - the automation that replaces the old "auto
 * status on page load" logic. Scheduled to run daily (see
 * routes/console.php); the command itself decides whether there's
 * anything to do, so it's safe to leave the schedule running every day
 * rather than trying to target one exact date.
 *
 * Follow-up Round 5 rewrite: this used to create exactly one kind of
 * alert (never-submitted/not-green-or-yellow, gated to day 6+). It now
 * covers the whole DILG submission window for the single monthly
 * period currently sitting inside it (last calendar month, relative to
 * today), using ReportDeadlineService as the one place that math
 * happens:
 *
 *   - the window's start day (config('racts.submission_window_start_day'),
 *     the 1st by default) -> "submission period open"
 *   - the deadline day (config('racts.submission_deadline_day'), the
 *     5th by default), if still not submitted -> "deadline today"
 *   - any day after the deadline, if STILL never submitted at all ->
 *     "late" (report_records has no row for this barangay/report/period)
 *   - a safety net, checked every run: any record that WAS submitted
 *     late gets a "late submission" alert. The primary place that
 *     alert gets created is real-time, the moment the date is saved
 *     (see ReportGridService::saveRow()) - this is just a backstop for
 *     anything that somehow missed that, so a day this command doesn't
 *     otherwise touch (2nd-4th, or long after the window closes) can
 *     still catch it.
 *
 * Deliberately does NOT use status_color (green/yellow/red) to decide
 * lateness any more - a report graded red but submitted before the
 * deadline is a content/quality problem, not a lateness problem, and
 * the DILG rule this command implements is purely about the date a
 * report was submitted relative to the 1st-5th window. status_color
 * itself is untouched everywhere else in RACTS (grids, ranking,
 * exports) - this command just stopped reading it for this one
 * decision.
 *
 * Yearly reports are explicitly out of scope - the client has no
 * lateness rule for them (Section 6, confirmed decision #4) - so only
 * `frequency = 'monthly'` slots are ever considered here.
 */
class CheckLateSubmissions extends Command
{
    protected $signature = 'racts:check-late-submissions';

    protected $description = 'Automatically create submission-window-open, deadline-today, late, and late-submission notifications for the monthly report period currently inside its DILG submission window.';

    public function handle(ReportDeadlineService $deadlines): int
    {
        $today = Carbon::today();
        $closedPeriod = $today->copy()->subMonthNoOverflow();
        $period = $closedPeriod->format('Y-m');
        $periodLabel = $closedPeriod->format('F Y');

        $windowStart = $deadlines->windowStartFor($period, ReportRequirement::FREQUENCY_MONTHLY);
        $deadline = $deadlines->deadlineFor($period, ReportRequirement::FREQUENCY_MONTHLY);

        $this->info(sprintf(
            'Checking %s (period %s) - submission window %s to %s. Today is %s.',
            $periodLabel,
            $period,
            $windowStart->format('M j'),
            $deadline->format('M j, Y'),
            $today->format('M j, Y')
        ));

        $barangays = Barangay::where('status', Barangay::STATUS_ACTIVE)->get();
        $users = User::all();

        if ($barangays->isEmpty() || $users->isEmpty()) {
            $this->info('No active barangays or users on file - nothing to do.');

            return self::SUCCESS;
        }

        $created = 0;
        $alreadyOnFile = 0;

        foreach (array_keys(ReportRequirement::COUNCIL_TYPES) as $councilType) {
            $requirements = ReportRequirement::forCouncil($councilType)->active()->monthly()->get();

            if ($requirements->isEmpty()) {
                continue;
            }

            $records = ReportRecord::whereIn('requirement_id', $requirements->pluck('id'))
                ->where('period', $period)
                ->get()
                ->keyBy(fn (ReportRecord $r) => $r->barangay_id.':'.$r->requirement_id);

            foreach ($barangays as $barangay) {
                foreach ($requirements as $requirement) {
                    $record = $records->get($barangay->id.':'.$requirement->id);

                    if ($today->day === $deadlines->windowStartDay()) {
                        [$created, $alreadyOnFile] = $this->notifyEvent(
                            Notification::TYPE_PERIOD_OPEN, $barangay, $requirement, $period, $record, $users,
                            sprintf(
                                'Submission Period Open: %s (%s Council) — the %s report for %s can now be submitted. Submission period: %s–%s.',
                                $barangay->barangay_name, $requirement->councilLabel(), $requirement->report_name,
                                $periodLabel, $windowStart->format('F j'), $deadline->format('j, Y')
                            ),
                            $created, $alreadyOnFile
                        );
                    }

                    if ($today->day === $deadlines->deadlineDay() && ! $record) {
                        [$created, $alreadyOnFile] = $this->notifyEvent(
                            Notification::TYPE_DEADLINE_TODAY, $barangay, $requirement, $period, $record, $users,
                            sprintf(
                                'Submission Deadline Today: %s (%s Council) — the deadline for the %s report for %s is today, %s. Reports submitted after today will be considered late.',
                                $barangay->barangay_name, $requirement->councilLabel(), $requirement->report_name,
                                $periodLabel, $deadline->format('F j, Y')
                            ),
                            $created, $alreadyOnFile
                        );
                    }

                    if ($today->day > $deadlines->deadlineDay() && ! $record) {
                        [$created, $alreadyOnFile] = $this->notifyEvent(
                            Notification::TYPE_REPORT_LATE, $barangay, $requirement, $period, $record, $users,
                            sprintf(
                                'Late: %s (%s Council) did not submit "%s" for %s within the %s–%s submission period. It is now considered late.',
                                $barangay->barangay_name, $requirement->councilLabel(), $requirement->report_name,
                                $periodLabel, $windowStart->format('F j'), $deadline->format('j, Y')
                            ),
                            $created, $alreadyOnFile
                        );
                    }

                    if ($record && $deadlines->statusFor($period, $requirement->frequency, $record->submission_date) === ReportDeadlineService::STATUS_LATE) {
                        [$created, $alreadyOnFile] = $this->notifyEvent(
                            Notification::TYPE_LATE_SUBMISSION, $barangay, $requirement, $period, $record, $users,
                            sprintf(
                                'Late Submission: %s (%s Council) submitted "%s" for %s on %s, after the %s deadline.',
                                $barangay->barangay_name, $requirement->councilLabel(), $requirement->report_name,
                                $periodLabel, $record->submission_date->format('F j, Y'), $deadline->format('F j, Y')
                            ),
                            $created, $alreadyOnFile
                        );
                    }
                }
            }
        }

        $this->info("Done. {$created} new notification(s) created, {$alreadyOnFile} event(s) already on file and skipped.");

        return self::SUCCESS;
    }

    /**
     * Creates $message for every user in $users who hasn't already been
     * notified about this exact (type, barangay, requirement, period)
     * combination - the structured dedup key added in Round 5, which
     * replaces comparing message TEXT (see this round's migration for
     * why that was fragile). withTrashed() is deliberate: a cleared
     * (soft-deleted) notification must still count as "already sent" so
     * Clear Notifications doesn't cause the same alert to come back
     * (see the Notification model and Round 3's fix).
     *
     * $created/$alreadyOnFile are running totals threaded through the
     * caller's loop rather than instance state, so this stays a plain
     * stateless helper.
     *
     * @return array{0: int, 1: int} the updated [$created, $alreadyOnFile]
     */
    private function notifyEvent(
        string $type,
        Barangay $barangay,
        ReportRequirement $requirement,
        string $period,
        ?ReportRecord $record,
        \Illuminate\Support\Collection $users,
        string $message,
        int $created,
        int $alreadyOnFile
    ): array {
        $alreadyNotifiedUserIds = Notification::withTrashed()
            ->where('auto_generated', true)
            ->where('type', $type)
            ->where('barangay_id', $barangay->id)
            ->where('requirement_id', $requirement->id)
            ->where('period', $period)
            ->pluck('user_id')
            ->all();

        $newlyNotified = 0;

        foreach ($users as $user) {
            if (in_array($user->id, $alreadyNotifiedUserIds, true)) {
                continue;
            }

            // Barangay accounts only hear about their own barangay; DILG
            // accounts are alerted about every barangay.
            if ($user->isBarangay() && $user->barangay_id !== $barangay->id) {
                continue;
            }

            Notification::create([
                'user_id' => $user->id,
                'report_record_id' => $record?->id,
                'type' => $type,
                'barangay_id' => $barangay->id,
                'requirement_id' => $requirement->id,
                'period' => $period,
                'message' => $message,
                'notification_date' => now(),
                'is_read' => false,
                'auto_generated' => true,
            ]);

            $created++;
            $newlyNotified++;
        }

        if ($newlyNotified === 0) {
            $alreadyOnFile++;
        }

        return [$created, $alreadyOnFile];
    }
}
