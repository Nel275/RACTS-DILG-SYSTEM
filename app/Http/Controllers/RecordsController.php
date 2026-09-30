<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\ReportRecord;
use App\Models\ReportRequirement;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Records" - a read-only page added after the redesign was already
 * delivered, in response to a follow-up request: the Barangay/SK
 * Reports grids only ever show ONE "Period to Edit" at a time, which
 * made it easy to lose track of which months had already been filled
 * in across a lot of barangays and report slots. This page is the
 * opposite of the grid - not for editing, just for browsing everything
 * that's been recorded, filtered and paginated, plus a per-period
 * count so "did we already do August?" has a one-glance answer.
 *
 * Deliberately NOT a revival of the old (removed) Submission History
 * page - that page's job (deadline/on-time tracking) doesn't exist any
 * more; this one has a narrower, purely informational purpose and has
 * no logic of its own beyond reading report_records. All editing still
 * only happens on the grid pages.
 */
class RecordsController extends Controller
{
    public function index(Request $request): View
    {
        $period = $this->validPeriod($request->query('period'));
        $barangayId = $request->query('barangay_id') !== null && $request->query('barangay_id') !== ''
            ? (int) $request->query('barangay_id')
            : null;

        $barangay = $this->councilData(ReportRequirement::COUNCIL_BARANGAY, $period, $barangayId, 'barangay_page');
        $sk = $this->councilData(ReportRequirement::COUNCIL_SK, $period, $barangayId, 'sk_page');

        return view('records.index', [
            'barangay' => $barangay,
            'sk' => $sk,
            'barangays' => Barangay::where('status', Barangay::STATUS_ACTIVE)->orderBy('barangay_name')->get(),
            'periods' => ReportRecord::query()->select('period')->distinct()->orderByDesc('period')->pluck('period'),
            'selectedPeriod' => $period,
            'selectedBarangayId' => $barangayId,
        ]);
    }

    /**
     * @return array{records: \Illuminate\Contracts\Pagination\LengthAwarePaginator, summary: \Illuminate\Support\Collection}
     */
    private function councilData(string $councilType, ?string $period, ?int $barangayId, string $pageName): array
    {
        $slotIds = ReportRequirement::forCouncil($councilType)->pluck('id');

        $records = ReportRecord::with(['barangay', 'reportRequirement', 'editedBy'])
            ->whereIn('requirement_id', $slotIds)
            ->when($period, fn ($q) => $q->where('period', $period))
            ->when($barangayId, fn ($q) => $q->where('barangay_id', $barangayId))
            ->orderByDesc('period')
            ->orderBy('barangay_id')
            ->paginate(25, ['*'], $pageName)
            ->withQueryString();

        // Independent of the filters above - this is meant to answer
        // "which periods exist at all for this council", so narrowing it
        // by the same period filter would be pointless (it'd always
        // show just the one selected period).
        $summary = ReportRecord::whereIn('requirement_id', $slotIds)
            ->when($barangayId, fn ($q) => $q->where('barangay_id', $barangayId))
            ->selectRaw('period, count(*) as entry_count')
            ->groupBy('period')
            ->orderByDesc('period')
            ->get();

        return ['records' => $records, 'summary' => $summary];
    }

    /**
     * Accepts either "YYYY-MM" (a monthly slot's period) or "YYYY" (a
     * yearly slot's) - the period filter applies to both councils' data
     * at once, and either shape can be valid depending on what's
     * selected.
     */
    private function validPeriod(?string $value): ?string
    {
        if (! $value || ! preg_match('/^\d{4}(-\d{2})?$/', $value)) {
            return null;
        }

        return $value;
    }
}
