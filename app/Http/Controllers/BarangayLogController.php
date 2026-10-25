<?php

namespace App\Http\Controllers;

use App\Rules\PlainText;
use App\Models\AuditLog;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\Notification;
use App\Models\ReportRequirement;
use App\Services\ReportEntryGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Follow-up round: manages the Barangay Logs catalog - the trackable
 * "items" (report/document types) that populate the Report dropdown on
 * the Barangay Log Details list. Both roles can view; only the
 * administrator can add, rename, or deactivate items - same split as
 * report slot management on the grids.
 *
 * Follow-up round: there is no standalone catalog page any more - the
 * "Manage Reports" panel on the Barangay Log Details tab
 * (barangay-log-details/index.blade.php) is now the only place this
 * catalog is viewed and edited, reusing the $reportOptions list that
 * page's own controller already builds. Only the three mutating actions
 * live here now; they all redirect back to that page.
 *
 * Follow-up round: show() is new - clicking a report in the Report
 * Filing list now opens this one-report roster instead of jumping
 * straight to a single barangay's entry, so you can see every barangay
 * logged against that report at a glance.
 *
 * Follow-up round: this catalog now covers both councils (see
 * BarangayLog::council_type), so update()/toggle() redirect back to
 * whichever council's Report Filing tab the item actually belongs to,
 * rather than always landing on the Barangay one. council_type itself
 * is fixed once an item exists (same rule as a ReportRequirement
 * slot's), so it's never part of the update() validation below.
 */
class BarangayLogController extends Controller
{
    public function show(BarangayLog $barangayLog): View
    {
        // Same self-healing idea as BarangayLogDetailController::index():
        // flip anything still "pending" past its deadline to "missed"
        // right before the list is built, scoped to just this report's
        // entries.
        BarangayLogDetail::where('barangay_log_id', $barangayLog->id)
            ->where('status', BarangayLogDetail::STATUS_PENDING)
            ->whereDate('deadline_date', '<', now()->toDateString())
            ->update(['status' => BarangayLogDetail::STATUS_MISSED]);

        $entries = $barangayLog->details()
            ->with('barangay')
            ->get()
            ->sortBy(fn ($entry) => $entry->barangay->barangay_name)
            ->values();

        return view('barangay-logs.show', compact('barangayLog', 'entries'));
    }

    /**
     * Adds an official report requirement (admin only - see routes). A
     * monthly/annual one immediately fills every barangay's My Reports
     * with its current-period entries via ReportEntryGenerator.
     */
    public function store(Request $request, ReportEntryGenerator $generator): RedirectResponse
    {
        // Older callers send only a name and a one-time deadline.
        $request->mergeIfMissing(['frequency' => BarangayLog::FREQUENCY_ONCE, 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'is_mandatory' => true]);

        $validated = $request->validate($this->requirementRules() + [
            'council_type' => ['required', Rule::in([ReportRequirement::COUNCIL_BARANGAY, ReportRequirement::COUNCIL_SK, 'both'])],
        ]);

        $councils = $validated['council_type'] === 'both'
            ? [ReportRequirement::COUNCIL_BARANGAY, ReportRequirement::COUNCIL_SK]
            : [$validated['council_type']];

        foreach ($councils as $council) {
            $exists = BarangayLog::forCouncil($council)
                ->whereRaw('LOWER(item_description) = ?', [strtolower(trim($validated['item_description']))])
                ->exists();
            if ($exists) {
                return back()->withInput()->withErrors([
                    'item_description' => sprintf('A %s report named "%s" already exists - edit that one instead.',
                        ReportRequirement::COUNCIL_TYPES[$council], $validated['item_description']),
                ]);
            }
        }

        foreach ($councils as $council) {
            $item = BarangayLog::create($this->requirementAttributes($validated) + [
                'council_type' => $council,
                'status' => BarangayLog::STATUS_ACTIVE,
                'date_process' => now(),
                'processed_by' => $request->user()->name,
            ]);

            AuditLog::record($request->user(), sprintf('Added %s report requirement "%s" (%s, %s).',
                $item->councilLabel(), $item->item_description, $item->frequencyLabel(), $item->is_mandatory ? 'mandatory' : 'optional'));
        }

        $created = $generator->generate();

        return redirect()->route($this->filingIndexRouteFor($item))->with('success', $created > 0
            ? "Report requirement added. {$created} My Reports entries were created."
            : 'Report requirement added.');
    }

    public function update(Request $request, BarangayLog $barangayLog, ReportEntryGenerator $generator): RedirectResponse
    {
        $request->mergeIfMissing($barangayLog->only(['frequency', 'deadline_day', 'deadline_month', 'is_mandatory'])
            + ['effective_from' => $barangayLog->effective_from?->toDateString()]);

        $validated = $request->validate($this->requirementRules() + [
            'status' => ['required', Rule::in(array_keys(BarangayLog::STATUSES))],
        ]);

        // Only the requirement itself changes - entries already generated
        // keep their own name snapshot and deadline (history is preserved).
        $barangayLog->update($this->requirementAttributes($validated) + [
            'status' => $validated['status'],
            'date_process' => now(),
            'processed_by' => $request->user()->name,
        ]);

        AuditLog::record($request->user(), sprintf('Updated %s report requirement "%s" (%s, deadline: %s).',
            $barangayLog->councilLabel(), $barangayLog->item_description, $barangayLog->frequencyLabel(), $barangayLog->deadlineRuleLabel()));

        if ($barangayLog->isRecurring() && $barangayLog->wasChanged(['item_description', 'frequency', 'deadline_day', 'deadline_month', 'effective_from', 'is_mandatory'])) {
            Notification::notifyBarangay(null, Notification::TYPE_REQUIREMENT_CHANGED, sprintf(
                'DILG updated the %s report requirement "%s": %s, due %s. Reports already listed keep their deadlines.',
                $barangayLog->councilLabel(), $barangayLog->item_description, $barangayLog->frequencyLabel(), lcfirst($barangayLog->deadlineRuleLabel())
            ));
        }

        $generator->generate();

        return redirect()->route($this->filingIndexRouteFor($barangayLog))->with('success', 'Report requirement updated.');
    }

    public function toggle(Request $request, BarangayLog $barangayLog, ReportEntryGenerator $generator): RedirectResponse
    {
        $barangayLog->update([
            'status' => $barangayLog->isActive() ? BarangayLog::STATUS_INACTIVE : BarangayLog::STATUS_ACTIVE,
            'date_process' => now(),
            'processed_by' => $request->user()->name,
        ]);

        AuditLog::record($request->user(), sprintf(
            '%s barangay log item "%s".',
            $barangayLog->isActive() ? 'Reactivated' : 'Deactivated',
            $barangayLog->item_description
        ));

        if ($barangayLog->isActive()) {
            $generator->generate();
        }

        return redirect()->route($this->filingIndexRouteFor($barangayLog))->with(
            'success',
            $barangayLog->isActive() ? 'Log item reactivated.' : 'Log item deactivated.'
        );
    }

    /** @return array<string, mixed> */
    private function requirementRules(): array
    {
        return [
            'item_description' => ['required', 'string', 'max:255', new PlainText],
            'frequency' => ['required', Rule::in(array_keys(BarangayLog::FREQUENCIES))],
            'deadline_date' => ['nullable', 'required_if:frequency,once', 'date'],
            'deadline_day' => ['nullable', 'required_unless:frequency,once', 'integer', 'between:1,28'],
            'deadline_month' => ['nullable', 'required_if:frequency,annual', 'integer', 'between:1,12'],
            'effective_from' => ['nullable', 'date'], // back-fill is capped by ReportEntryGenerator::MAX_MONTHS_BACK
            'is_mandatory' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function requirementAttributes(array $validated): array
    {
        $recurring = $validated['frequency'] !== BarangayLog::FREQUENCY_ONCE;

        return [
            'item_description' => trim($validated['item_description']),
            'frequency' => $validated['frequency'],
            'deadline_date' => $recurring ? null : $validated['deadline_date'],
            'deadline_day' => $recurring ? (int) $validated['deadline_day'] : null,
            'deadline_month' => $validated['frequency'] === BarangayLog::FREQUENCY_ANNUAL ? (int) $validated['deadline_month'] : null,
            'effective_from' => $recurring ? ($validated['effective_from'] ?? now()->toDateString()) : null,
            'is_mandatory' => (bool) ($validated['is_mandatory'] ?? false),
        ];
    }

    private function filingIndexRouteFor(BarangayLog $barangayLog): string
    {
        return $barangayLog->council_type === ReportRequirement::COUNCIL_SK
            ? 'sk-log-details.index'
            : 'barangay-log-details.index';
    }
}
