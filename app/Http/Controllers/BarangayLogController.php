<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BarangayLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
 */
class BarangayLogController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_description' => ['required', 'string', 'max:255'],
            'deadline_date' => ['required', 'date'],
        ]);

        $item = BarangayLog::create([
            'item_description' => $validated['item_description'],
            'deadline_date' => $validated['deadline_date'],
            'status' => BarangayLog::STATUS_ACTIVE,
            'date_process' => now(),
            'processed_by' => $request->user()->name,
        ]);

        AuditLog::record($request->user(), sprintf('Added barangay log item "%s".', $item->item_description));

        return redirect()->route('barangay-log-details.index')->with('success', 'Barangay log item added.');
    }

    public function update(Request $request, BarangayLog $barangayLog): RedirectResponse
    {
        $validated = $request->validate([
            'item_description' => ['required', 'string', 'max:255'],
            'deadline_date' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(BarangayLog::STATUSES))],
        ]);

        $barangayLog->update([
            'item_description' => $validated['item_description'],
            'deadline_date' => $validated['deadline_date'],
            'status' => $validated['status'],
            'date_process' => now(),
            'processed_by' => $request->user()->name,
        ]);

        AuditLog::record($request->user(), sprintf('Updated barangay log item "%s".', $barangayLog->item_description));

        return redirect()->route('barangay-log-details.index')->with('success', 'Barangay log item updated.');
    }

    public function toggle(Request $request, BarangayLog $barangayLog): RedirectResponse
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

        return redirect()->route('barangay-log-details.index')->with(
            'success',
            $barangayLog->isActive() ? 'Log item reactivated.' : 'Log item deactivated.'
        );
    }
}
