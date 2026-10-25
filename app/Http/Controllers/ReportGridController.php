<?php

namespace App\Http\Controllers;

use App\Rules\PlainText;
use App\Models\AuditLog;
use App\Models\ReportRequirement;
use App\Services\RankingCalculator;
use App\Services\ReportGridService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Barangay Reports and SK Reports - the two grid pages that replace the
 * old Submissions, Submission History, and Report Categories pages
 * (client spec Section 5). Rows are barangays, columns are that
 * council's active report slots, each cell holds a submission date and
 * a green/yellow/red grade. Admins also manage the report slots
 * themselves (rename, add, deactivate) from a panel on the same page,
 * rather than a separate admin screen.
 */
class ReportGridController extends Controller
{
    public function __construct(
        private readonly ReportGridService $grid,
        private readonly RankingCalculator $ranking,
    ) {
    }

    public function barangay(Request $request): View
    {
        return $this->show($request, ReportRequirement::COUNCIL_BARANGAY, 'report-grid.barangay');
    }

    public function sk(Request $request): View
    {
        return $this->show($request, ReportRequirement::COUNCIL_SK, 'report-grid.sk');
    }

    private function show(Request $request, string $councilType, string $viewName): View
    {
        $periodMonth = $this->validMonth($request->query('month')) ?? now()->format('Y-m');
        $rangeStart = $this->validMonth($request->query('start')) ?? now()->startOfYear()->format('Y-m');
        $rangeEnd = $this->validMonth($request->query('end')) ?? now()->format('Y-m');

        $columns = $this->grid->columnsFor($councilType);
        $allSlots = $this->grid->allSlotsFor($councilType);
        $barangays = $this->grid->barangaysFor();
        $cells = $this->grid->cellsFor($columns, $periodMonth);
        $ranking = $this->ranking->rank($councilType, $rangeStart, $rangeEnd)
            ->keyBy(fn (array $row) => $row['barangay']->id);

        return view($viewName, [
            'councilType' => $councilType,
            'periodMonth' => $periodMonth,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'columns' => $columns,
            'allSlots' => $allSlots,
            'barangays' => $barangays,
            'cells' => $cells,
            'ranking' => $ranking,
        ]);
    }

    private function validMonth(?string $value): ?string
    {
        if (! $value || ! preg_match('/^\d{4}-\d{2}$/', $value)) {
            return null;
        }

        return $value;
    }

    public function saveBarangay(Request $request): RedirectResponse
    {
        return $this->save($request, ReportRequirement::COUNCIL_BARANGAY, 'barangay-reports.index');
    }

    public function saveSk(Request $request): RedirectResponse
    {
        return $this->save($request, ReportRequirement::COUNCIL_SK, 'sk-reports.index');
    }

    private function save(Request $request, string $councilType, string $routeName): RedirectResponse
    {
        $validated = $request->validate([
            'barangay_id' => 'required|exists:barangays,id',
            'period_month' => ['required', 'date_format:Y-m'],
            'cells' => 'array',
            'cells.*.date' => 'nullable|date',
            'cells.*.color' => 'nullable|in:'.implode(',', array_keys(\App\Models\ReportRecord::COLORS)),
        ]);

        $columns = $this->grid->columnsFor($councilType);

        $this->grid->saveRow(
            (int) $validated['barangay_id'],
            $columns,
            $validated['period_month'],
            $validated['cells'] ?? [],
            (int) auth()->id()
        );

        return redirect()
            ->route($routeName, $request->only(['month', 'start', 'end']))
            ->with('success', 'Report entries saved.');
    }

    // --- Admin slot management: add / rename / deactivate report slots ---

    public function storeSlot(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_name' => ['required', 'string', 'max:255', new PlainText],
            'council_type' => ['required', 'in:'.implode(',', array_keys(ReportRequirement::COUNCIL_TYPES))],
            'frequency' => ['required', 'in:'.implode(',', array_keys(ReportRequirement::FREQUENCIES))],
            'display_order' => 'nullable|integer|min:0',
        ]);

        $slot = ReportRequirement::create([
            'report_name' => $validated['report_name'],
            'council_type' => $validated['council_type'],
            'frequency' => $validated['frequency'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => true,
        ]);

        AuditLog::record($request->user(), sprintf(
            'Added report slot "%s" (%s Council, %s).',
            $slot->report_name,
            $slot->councilLabel(),
            $slot->frequencyLabel()
        ));

        return $this->backToGrid($validated['council_type'], 'Report slot added.');
    }

    public function updateSlot(Request $request, ReportRequirement $reportRequirement): RedirectResponse
    {
        // Council type and frequency are fixed once a slot exists - both
        // determine how its period string is built, and reports already
        // recorded against it depend on that staying consistent. Renaming
        // and reordering are the only things an admin can change here;
        // a report that genuinely needs a different frequency is better
        // added as a new slot and the old one deactivated.
        $validated = $request->validate([
            'report_name' => ['required', 'string', 'max:255', new PlainText],
            'display_order' => 'nullable|integer|min:0',
        ]);

        $reportRequirement->update([
            'report_name' => $validated['report_name'],
            'display_order' => $validated['display_order'] ?? $reportRequirement->display_order,
        ]);

        AuditLog::record($request->user(), sprintf(
            'Updated report slot "%s" (%s Council).',
            $reportRequirement->report_name,
            $reportRequirement->councilLabel()
        ));

        return $this->backToGrid($reportRequirement->council_type, 'Report slot updated.');
    }

    public function toggleSlot(Request $request, ReportRequirement $reportRequirement): RedirectResponse
    {
        $reportRequirement->update(['is_active' => ! $reportRequirement->is_active]);

        $message = $reportRequirement->is_active ? 'Report slot reactivated.' : 'Report slot deactivated.';

        AuditLog::record($request->user(), sprintf(
            '%s report slot "%s" (%s Council).',
            $reportRequirement->is_active ? 'Reactivated' : 'Deactivated',
            $reportRequirement->report_name,
            $reportRequirement->councilLabel()
        ));

        return $this->backToGrid($reportRequirement->council_type, $message);
    }

    private function backToGrid(string $councilType, string $message): RedirectResponse
    {
        $routeName = $councilType === ReportRequirement::COUNCIL_SK ? 'sk-reports.index' : 'barangay-reports.index';

        return redirect()->route($routeName)->with('success', $message);
    }
}
