<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use App\Services\RankingCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RankingCalculator $ranking,
    ) {
    }

    /**
     * Client spec Section 3.5: ranking summary (top barangays, both
     * councils - added Phase 2 once ComplianceCalculator/
     * NotificationGenerator were retired) plus current-cycle progress
     * (added this phase) - how many of this month's expected monthly
     * reports are in vs. still pending, so staff can see progress
     * mid-cycle before the period closes.
     */
    public function index(): View
    {
        $totalBarangays = Barangay::where('status', Barangay::STATUS_ACTIVE)->count();
        $totalReportSlots = ReportRequirement::active()->count();

        $yearStart = now()->startOfYear()->format('Y-m');
        $currentMonth = now()->format('Y-m');
        $currentPeriodLabel = now()->format('F Y');

        $barangayRanking = $this->ranking
            ->rank(ReportRequirement::COUNCIL_BARANGAY, $yearStart, $currentMonth)
            ->take(5);

        $skRanking = $this->ranking
            ->rank(ReportRequirement::COUNCIL_SK, $yearStart, $currentMonth)
            ->take(5);

        $barangayProgress = $this->currentCycleProgress(ReportRequirement::COUNCIL_BARANGAY, $currentMonth);
        $skProgress = $this->currentCycleProgress(ReportRequirement::COUNCIL_SK, $currentMonth);

        // Sourced from AuditLog, not the live report_records table - so
        // "Clear History" below can wipe this feed without touching a
        // single actual grade. AuditLog's one string 'action' column
        // already carries the readable detail (written by
        // ReportGridService::saveRow() for report entries, and by every
        // admin controller - users, barangays, report slots - as of
        // this follow-up round), so no join is needed here.
        //
        // Deliberately just the 5 most recent, to keep the dashboard a
        // preview rather than a second copy of the full log - see the
        // new Activity Logs page (ActivityLogController) for the
        // complete, paginated history the "View All" button opens.
        //
        // Ordered by log_date then id: log_date is a plain dateTime
        // column (whole-second precision), so two actions in the same
        // second would tie on log_date alone with no guaranteed order -
        // id (auto-increment) always matches insertion order, so it
        // breaks the tie correctly.
        $recentActivity = AuditLog::with('user')
            ->orderByDesc('log_date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBarangays',
            'totalReportSlots',
            'currentPeriodLabel',
            'barangayRanking',
            'skRanking',
            'barangayProgress',
            'skProgress',
            'recentActivity'
        ));
    }

    /**
     * Wipes the recent-activity feed shown on the dashboard. Deliberately
     * only touches audit_logs - report_records (the actual graded data
     * everything else in RACTS depends on) is never affected. Admin-only,
     * matching every other destructive action in the app (deleting a
     * barangay, etc.) - a judgment call, not a spec requirement; open it
     * up to staff too if that turns out to be too restrictive in
     * practice.
     */
    public function clearActivity(): RedirectResponse
    {
        AuditLog::query()->delete();

        return redirect()->route('dashboard')->with('success', 'Recent activity history cleared.');
    }

    /**
     * How far along this council is on the current month's monthly
     * reports (client spec Section 3.5). Scoped to monthly slots only -
     * "current-cycle progress" is a monthly-cycle idea, and mirrors
     * Section 4's automation, which is monthly-only for the same
     * reason: yearly reports don't have a monthly due date to be
     * "mid-cycle" toward.
     *
     * "Pending" here covers both a cell with nothing recorded at all
     * and a cell with a submission_date but no status_color yet
     * (recorded, not graded) - both are "not yet confirmed compliant",
     * which is what this widget is answering. Green/yellow count as
     * submitted together (the spec's own wording: "submitted
     * (green/yellow)"); red is broken out separately as a confirmed
     * miss, since that's a meaningfully different state from "nobody's
     * checked yet" everywhere else in RACTS.
     *
     * @return array{expected: int, submitted: int, not_submitted: int, pending: int, percent_submitted: float|null}
     */
    private function currentCycleProgress(string $councilType, string $period): array
    {
        $barangayCount = Barangay::where('status', Barangay::STATUS_ACTIVE)->count();

        $monthlySlotIds = ReportRequirement::forCouncil($councilType)
            ->active()
            ->monthly()
            ->pluck('id');

        $expected = $barangayCount * $monthlySlotIds->count();

        $records = ReportRecord::whereIn('requirement_id', $monthlySlotIds)
            ->where('period', $period)
            ->get();

        $submitted = $records->whereIn('status_color', ['green', 'yellow'])->count();
        $notSubmitted = $records->where('status_color', 'red')->count();
        $pending = max($expected - $submitted - $notSubmitted, 0);

        return [
            'expected' => $expected,
            'submitted' => $submitted,
            'not_submitted' => $notSubmitted,
            'pending' => $pending,
            'percent_submitted' => $expected > 0 ? round(($submitted / $expected) * 100, 1) : null,
        ];
    }
}
