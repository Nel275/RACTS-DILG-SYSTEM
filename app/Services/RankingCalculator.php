<?php

namespace App\Services;

use App\Models\Barangay;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use Illuminate\Support\Collection;

/**
 * Implements the scoring/ranking rules from the client's redesign spec
 * (Section 2):
 *
 *   - green = 100%, yellow = 50%, red = 0% per graded report.
 *   - A barangay's percentage = the average score across every
 *     applicable report within the selected filter range.
 *   - Yearly reports always count toward the percentage, regardless of
 *     which months are selected in the Start/End Month filter.
 *   - Ranking: highest percentage first; ties are broken by earliest
 *     average submission date (percentage decides rank, submission
 *     date only resolves a tie).
 *   - Barangay and SK are ranked completely separately.
 *
 * One thing the spec doesn't spell out: what a report slot with no
 * status_color set yet (null - not graded, as opposed to red, which
 * means "confirmed not submitted") should count as. This implementation
 * excludes ungraded slots from the percentage average entirely, rather
 * than counting them as 0 - so a barangay isn't penalized for a report
 * nobody has checked yet. A barangay with nothing graded at all in the
 * selected range gets a null percentage (shown as "-", same convention
 * already used elsewhere in RACTS for "no data"). Flagged in
 * REDESIGN_PHASE1_DATA_MODEL.md as a judgment call, same as every other
 * place this project has had to fill in a gap the spec didn't cover.
 */
class RankingCalculator
{
    /**
     * Build the ranked list of barangays for one council type.
     *
     * @param  string  $councilType  ReportRequirement::COUNCIL_BARANGAY or COUNCIL_SK
     * @param  string|null  $startPeriod  "YYYY-MM", inclusive lower bound for monthly slots (null = no lower bound)
     * @param  string|null  $endPeriod  "YYYY-MM", inclusive upper bound for monthly slots (null = no upper bound)
     * @param  int|null  $requirementId  Follow-up round: when given, scores
     *     this one report slot only instead of averaging across every
     *     active slot for the council. The caller is responsible for
     *     making sure it actually belongs to $councilType - if it
     *     doesn't (or isn't active), $requirements below ends up empty
     *     and every barangay simply gets a null ("-") percentage, the
     *     same as "nothing graded yet" - never an error.
     * @return Collection  Rows sorted by rank, each shaped as:
     *     ['rank', 'barangay', 'percentage', 'green_count', 'yellow_count',
     *      'red_count', 'graded_count', 'avg_submission_timestamp']
     */
    public function rank(
        string $councilType,
        ?string $startPeriod = null,
        ?string $endPeriod = null,
        ?int $requirementId = null
    ): Collection {
        $barangays = Barangay::where('status', Barangay::STATUS_ACTIVE)
            ->orderBy('barangay_name')
            ->get();

        $requirements = ReportRequirement::forCouncil($councilType)->active()->get();

        if ($requirementId !== null) {
            $requirements = $requirements->where('id', $requirementId);
        }

        $monthlyIds = $requirements->where('frequency', ReportRequirement::FREQUENCY_MONTHLY)->pluck('id');
        $yearlyIds = $requirements->where('frequency', ReportRequirement::FREQUENCY_YEARLY)->pluck('id');

        // Monthly slots are scoped to the selected period range; yearly
        // slots always count, in every range, per the client's spec.
        $records = ReportRecord::whereIn('barangay_id', $barangays->pluck('id'))
            ->where(function ($query) use ($monthlyIds, $yearlyIds, $startPeriod, $endPeriod) {
                $query->where(function ($q) use ($monthlyIds, $startPeriod, $endPeriod) {
                    $q->whereIn('requirement_id', $monthlyIds);
                    if ($startPeriod) {
                        $q->where('period', '>=', $startPeriod);
                    }
                    if ($endPeriod) {
                        $q->where('period', '<=', $endPeriod);
                    }
                })->orWhereIn('requirement_id', $yearlyIds);
            })
            ->get()
            ->groupBy('barangay_id');

        $rows = $barangays->map(function (Barangay $barangay) use ($records) {
            $barangayRecords = $records->get($barangay->id, collect());
            $graded = $barangayRecords->whereNotNull('status_color');

            $percentage = $graded->isNotEmpty()
                ? round($graded->avg(fn (ReportRecord $r) => $r->score()), 2)
                : null;

            $withDates = $graded->whereNotNull('submission_date');
            $avgSubmissionTimestamp = $withDates->isNotEmpty()
                ? $withDates->avg(fn (ReportRecord $r) => $r->submission_date->timestamp)
                : null;

            return [
                'barangay' => $barangay,
                'percentage' => $percentage,
                'green_count' => $graded->where('status_color', 'green')->count(),
                'yellow_count' => $graded->where('status_color', 'yellow')->count(),
                'red_count' => $graded->where('status_color', 'red')->count(),
                'graded_count' => $graded->count(),
                'avg_submission_timestamp' => $avgSubmissionTimestamp,
            ];
        });

        // Highest percentage first (no-data rows sort last); tie broken
        // by earliest average submission date (lower timestamp wins).
        $sorted = $rows->sort(function ($a, $b) {
            $byPercentage = ($b['percentage'] ?? -1) <=> ($a['percentage'] ?? -1);
            if ($byPercentage !== 0) {
                return $byPercentage;
            }

            return ($a['avg_submission_timestamp'] ?? PHP_INT_MAX)
                <=> ($b['avg_submission_timestamp'] ?? PHP_INT_MAX);
        })->values();

        return $sorted->map(function (array $row, int $index) {
            $row['rank'] = $index + 1;

            return $row;
        });
    }
}
