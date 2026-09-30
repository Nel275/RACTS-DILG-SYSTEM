<?php

namespace App\Http\Controllers;

use App\Models\ReportRequirement;
use App\Services\RankingCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Analytics & Ranking page (client spec Section 3.3) - replaces the old
 * Tally page. Same Start/End Month filter convention as the Barangay/SK
 * Reports grids, built entirely on top of Phase 1's RankingCalculator so
 * every page in RACTS agrees on what a barangay's percentage is.
 */
class AnalyticsController extends Controller
{
    public function __construct(
        private readonly RankingCalculator $ranking,
    ) {
    }

    public function index(Request $request): View
    {
        $start = $this->validMonth($request->query('start')) ?? now()->startOfYear()->format('Y-m');
        $end = $this->validMonth($request->query('end')) ?? now()->format('Y-m');

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        // Follow-up round: one report-type filter per council, each
        // resolved and validated independently so picking a report in
        // one tab never disturbs the other tab's selection (both query
        // params round-trip through every link/form on the page - see
        // analytics/index.blade.php and _council-panel.blade.php).
        $barangayRequirementId = $this->validRequirementId($request->query('barangay_report'), ReportRequirement::COUNCIL_BARANGAY);
        $skRequirementId = $this->validRequirementId($request->query('sk_report'), ReportRequirement::COUNCIL_SK);

        return view('analytics.index', [
            'start' => $start,
            'end' => $end,
            'barangayRequirementId' => $barangayRequirementId,
            'skRequirementId' => $skRequirementId,
            'barangay' => $this->buildCouncilData(ReportRequirement::COUNCIL_BARANGAY, $start, $end, $barangayRequirementId),
            'sk' => $this->buildCouncilData(ReportRequirement::COUNCIL_SK, $start, $end, $skRequirementId),
        ]);
    }

    /**
     * PDF export (client spec Section 3.4): a printable HTML page reusing
     * the same DILG letterhead (`layouts.print`) the old Report
     * Generation page used, with the current ranking table for whichever
     * council/range the export link was clicked from. "PDF" here means
     * the browser's own Print / Save as PDF, the same mechanism this
     * project has used since it first had a printable page - no new
     * dependency to render a PDF binary server-side.
     */
    public function exportPdf(Request $request): View
    {
        [$council, $start, $end, $requirementId, $requirementLabel] = $this->resolveExportFilters($request);

        return view('analytics.export-pdf', [
            'councilLabel' => $this->councilLabel($council),
            'reportLabel' => $requirementLabel ?? 'All Reports',
            'ranking' => $this->ranking->rank($council, $start, $end, $requirementId),
            'startLabel' => Carbon::createFromFormat('Y-m-d', $start.'-01')->format('F Y'),
            'endLabel' => Carbon::createFromFormat('Y-m-d', $end.'-01')->format('F Y'),
        ]);
    }

    /**
     * "Excel" export (client spec Section 3.4), written as CSV rather
     * than a true .xlsx binary - see REDESIGN_PHASE4_EXPORT.md for why.
     * Excel opens a CSV file natively, so this satisfies the same need
     * without a new Composer dependency.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        [$council, $start, $end, $requirementId, $requirementLabel] = $this->resolveExportFilters($request);

        $councilLabel = $this->councilLabel($council);
        $reportLabel = $requirementLabel ?? 'All Reports';
        $startLabel = Carbon::createFromFormat('Y-m-d', $start.'-01')->format('F Y');
        $endLabel = Carbon::createFromFormat('Y-m-d', $end.'-01')->format('F Y');
        $ranking = $this->ranking->rank($council, $start, $end, $requirementId);

        $filename = sprintf('racts-%s-analytics-%s-to-%s.csv', $council, $start, $end);

        return response()->streamDownload(function () use ($ranking, $councilLabel, $reportLabel, $startLabel, $endLabel) {
            $handle = fopen('php://output', 'w');

            // UTF-8 byte-order mark so Excel on Windows reads the file's
            // encoding correctly instead of mangling it.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Department of the Interior and Local Government - Santiago City Office']);
            fputcsv($handle, [$councilLabel.' Council - Analytics & Ranking Report']);
            fputcsv($handle, ['Report', $reportLabel]);
            fputcsv($handle, ['Reporting Period', $startLabel.' - '.$endLabel]);
            fputcsv($handle, ['Generated', now()->format('F j, Y g:i A')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Rank', 'Barangay', 'Percentage', 'Green', 'Yellow', 'Red', 'Graded']);

            foreach ($ranking as $row) {
                fputcsv($handle, [
                    $row['rank'],
                    $row['barangay']->barangay_name,
                    is_null($row['percentage']) ? '' : $row['percentage'],
                    $row['green_count'],
                    $row['yellow_count'],
                    $row['red_count'],
                    $row['graded_count'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Reads the exact same filters the Analytics page itself uses
     * (council, date range, and now the report-type filter), so an
     * export always represents whatever was on screen when the user
     * clicked it - the export links on the page carry all of these as
     * query params already (see analytics/_council-panel.blade.php).
     *
     * @return array{0: string, 1: string, 2: string, 3: int|null, 4: string|null} [councilType, start, end, requirementId, requirementLabel]
     */
    private function resolveExportFilters(Request $request): array
    {
        $council = $request->query('council');
        $council = in_array($council, array_keys(ReportRequirement::COUNCIL_TYPES), true)
            ? $council
            : ReportRequirement::COUNCIL_BARANGAY;

        $start = $this->validMonth($request->query('start')) ?? now()->startOfYear()->format('Y-m');
        $end = $this->validMonth($request->query('end')) ?? now()->format('Y-m');

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $requirementId = $this->validRequirementId($request->query('requirement'), $council);
        $requirementLabel = $requirementId
            ? ReportRequirement::find($requirementId)?->report_name
            : null;

        return [$council, $start, $end, $requirementId, $requirementLabel];
    }

    /**
     * Only accepts a requirement id that's active and actually belongs
     * to $councilType - a stale or tampered-with query string (e.g. a
     * Barangay report id pasted into a link while looking at the SK
     * tab) falls back to null ("All Reports") rather than silently
     * showing the wrong council's data or erroring.
     */
    private function validRequirementId(mixed $value, string $councilType): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $exists = ReportRequirement::forCouncil($councilType)
            ->active()
            ->where('id', (int) $value)
            ->exists();

        return $exists ? (int) $value : null;
    }

    private function councilLabel(string $councilType): string
    {
        return $councilType === ReportRequirement::COUNCIL_SK ? 'SK' : 'Barangay';
    }

    /**
     * @return array{ranking: \Illuminate\Support\Collection, chart: array, trend: array|null, reportOptions: \Illuminate\Support\Collection, selectedRequirementId: int|null, selectedRequirementLabel: string}
     */
    private function buildCouncilData(string $councilType, string $start, string $end, ?int $requirementId = null): array
    {
        $ranking = $this->ranking->rank($councilType, $start, $end, $requirementId);

        // The dropdown's own option list - every active report slot for
        // this council, same ordering as the grid columns.
        $reportOptions = ReportRequirement::forCouncil($councilType)
            ->active()
            ->orderBy('display_order')
            ->orderBy('report_name')
            ->get();

        $selectedLabel = $requirementId
            ? ($reportOptions->firstWhere('id', $requirementId)?->report_name ?? 'All Reports')
            : 'All Reports';

        return [
            'ranking' => $ranking,
            'chart' => [
                'labels' => $ranking->pluck('barangay.barangay_name')->values()->all(),
                // Ungraded ("no data yet") barangays plot as 0 on the bar
                // chart - the ranked table (with its own "-") is the place
                // to tell that apart from a real 0%, so a note under the
                // chart points there instead of building a second legend.
                'percentages' => $ranking->map(fn (array $row) => $row['percentage'] ?? 0)->values()->all(),
            ],
            // Only worth a trend line once more than one month is being
            // looked at - a single-month range has nothing to trend.
            'trend' => $start === $end ? null : $this->buildTrend($councilType, $start, $end, $requirementId),
            'reportOptions' => $reportOptions,
            'selectedRequirementId' => $requirementId,
            'selectedRequirementLabel' => $selectedLabel,
        ];
    }

    /**
     * One data point per calendar month in the selected range: that
     * month's council-wide average percentage (barangays with nothing
     * graded that month are left out of the average, same convention as
     * everywhere else in RACTS - see RankingCalculator). This reuses the
     * exact same percentage definition as the ranked table and bar chart
     * above it, which means yearly reports still contribute their flat,
     * unchanging score to every single month's average - intentional,
     * for consistency with the rest of the page, rather than inventing a
     * separate "monthly reports only" percentage just for this chart.
     *
     * @return array{labels: string[], values: (float|null)[]}
     */
    private function buildTrend(string $councilType, string $start, string $end, ?int $requirementId = null): array
    {
        $labels = [];
        $values = [];

        $cursor = Carbon::createFromFormat('Y-m-d', $start.'-01');
        $endCursor = Carbon::createFromFormat('Y-m-d', $end.'-01');

        while ($cursor->lte($endCursor)) {
            $period = $cursor->format('Y-m');
            $graded = $this->ranking->rank($councilType, $period, $period, $requirementId)
                ->filter(fn (array $row) => ! is_null($row['percentage']));

            $labels[] = $cursor->format('M Y');
            $values[] = $graded->isNotEmpty() ? round($graded->avg('percentage'), 2) : null;

            $cursor->addMonth();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function validMonth(?string $value): ?string
    {
        if (! $value || ! preg_match('/^\d{4}-\d{2}$/', $value)) {
            return null;
        }

        return $value;
    }
}
