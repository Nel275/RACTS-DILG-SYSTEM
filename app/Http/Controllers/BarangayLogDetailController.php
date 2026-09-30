<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Follow-up round: the Barangay Log Details list - built from the
 * "Purchases" page the client sent as a UI reference, remapped to this
 * app's own domain (Number -> Barangay, Supplier -> Report, with a
 * dropdown to pick the report/item type; Receiving Store, Total, and
 * Balance left out entirely, as asked). Lives as a second tab on the
 * existing Barangay Reports page - see report-grid/barangay.blade.php.
 */
class BarangayLogDetailController extends Controller
{
    public function index(Request $request): View
    {
        // Self-healing status, the same idea as the automatic late-
        // submission checks used elsewhere in RACTS, just without a
        // separate scheduled command: any entry still "pending" whose
        // deadline has already passed is flipped to "missed" right
        // before the list is built, so the Status column and stat
        // cards are always accurate without anyone running anything.
        BarangayLogDetail::where('status', BarangayLogDetail::STATUS_PENDING)
            ->whereDate('deadline_date', '<', now()->toDateString())
            ->update(['status' => BarangayLogDetail::STATUS_MISSED]);

        $status = $request->string('status')->trim()->value();
        $reportId = $request->integer('report') ?: null;
        $search = $request->string('search')->trim()->value();
        $from = $this->validDate($request->query('from'));
        $to = $this->validDate($request->query('to'));

        $query = BarangayLogDetail::query()
            ->with(['barangay', 'barangayLog'])
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->when($reportId, fn ($q, $reportId) => $q->where('barangay_log_id', $reportId))
            ->when($search, function ($q, $search) {
                $q->whereHas('barangay', fn ($bq) => $bq->where('barangay_name', 'like', "%{$search}%"));
            })
            ->when($from, fn ($q, $from) => $q->whereDate('deadline_date', '>=', $from))
            ->when($to, fn ($q, $to) => $q->whereDate('deadline_date', '<=', $to));

        // Stat cards reflect the current filter set - consistent with
        // how Analytics recalculates on filter elsewhere in RACTS -
        // computed before pagination narrows the query to one page.
        $statsBase = (clone $query);
        $stats = [
            'total' => (clone $statsBase)->count(),
            'submitted' => (clone $statsBase)->where('status', BarangayLogDetail::STATUS_SUBMITTED)->count(),
            'pending' => (clone $statsBase)->where('status', BarangayLogDetail::STATUS_PENDING)->count(),
            'overdue' => (clone $statsBase)->whereIn('status', [BarangayLogDetail::STATUS_LATE, BarangayLogDetail::STATUS_MISSED])->count(),
        ];

        $entries = $query->orderByDesc('deadline_date')->orderByDesc('id')->paginate(10)->withQueryString();

        $reportOptions = BarangayLog::orderBy('item_description')->get();
        $barangayOptions = Barangay::where('status', Barangay::STATUS_ACTIVE)->orderBy('barangay_name')->get();
        $activeLogItems = $reportOptions->where('status', BarangayLog::STATUS_ACTIVE);

        return view('barangay-log-details.index', compact(
            'entries',
            'stats',
            'status',
            'reportId',
            'search',
            'from',
            'to',
            'reportOptions',
            'barangayOptions',
            'activeLogItems'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barangay_id' => ['required', 'exists:barangays,id'],
            'barangay_log_id' => ['required', 'exists:barangay_logs,id'],
            'deadline_date' => ['required', 'date'],
        ]);

        $logItem = BarangayLog::findOrFail($validated['barangay_log_id']);

        $entry = BarangayLogDetail::create([
            'barangay_id' => $validated['barangay_id'],
            'barangay_log_id' => $logItem->id,
            'item_description' => $logItem->item_description,
            'deadline_date' => $validated['deadline_date'],
            'status' => BarangayLogDetail::STATUS_PENDING,
            'date_process' => now(),
            'processed_by' => $request->user()->name,
        ]);

        AuditLog::record($request->user(), sprintf(
            'Added barangay log entry "%s" for %s.',
            $entry->item_description,
            $entry->barangay->barangay_name
        ));

        return redirect()->route('barangay-log-details.index')
            ->with('success', 'Log entry added.');
    }

    public function show(BarangayLogDetail $barangayLogDetail): View
    {
        $barangayLogDetail->load(['barangay', 'barangayLog']);

        return view('barangay-log-details.show', ['entry' => $barangayLogDetail]);
    }

    public function markSubmitted(Request $request, BarangayLogDetail $barangayLogDetail): RedirectResponse
    {
        $validated = $request->validate([
            'date_submitted' => ['required', 'date'],
            // Optional on purpose: staff can record a submission first
            // and attach the file on a later edit if it isn't on hand
            // yet - see BarangayLogDetail::markSubmitted()'s doc comment.
            'report_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $reportFilePath = null;
        $reportOriginalName = null;

        if ($request->hasFile('report_file')) {
            // Staged, not filed yet - "Download" (fileIntoBarangayFolder)
            // is what moves this into the barangay's actual folder.
            $reportFilePath = $request->file('report_file')->store(
                'incoming-reports/'.$barangayLogDetail->id,
                'local'
            );
            $reportOriginalName = $request->file('report_file')->getClientOriginalName();
        }

        $barangayLogDetail->markSubmitted(
            Carbon::parse($validated['date_submitted']),
            $request->user()->name,
            $reportFilePath,
            $reportOriginalName
        );

        AuditLog::record($request->user(), sprintf(
            'Recorded submission for "%s" (%s) - %s.',
            $barangayLogDetail->item_description,
            $barangayLogDetail->barangay->barangay_name,
            $barangayLogDetail->statusLabel()
        ));

        return redirect()->route('barangay-log-details.show', $barangayLogDetail)
            ->with('success', 'Submission recorded.');
    }

    /**
     * The "Download" action on the Log Details list: files the
     * uploaded report into this entry's barangay's folder on the
     * server. Deliberately not a browser download - the report is
     * being filed/archived, not sent to whoever clicked the button.
     */
    public function download(Request $request, BarangayLogDetail $barangayLogDetail): RedirectResponse
    {
        if (! $barangayLogDetail->hasReportFile()) {
            return back()->with('error', 'This entry has no report file uploaded yet - attach one from the Mark Submitted form first.');
        }

        if ($barangayLogDetail->isFiled()) {
            return back()->with('error', 'This report has already been filed into its barangay folder.');
        }

        $barangayLogDetail->fileIntoBarangayFolder($request->user()->name);

        AuditLog::record($request->user(), sprintf(
            'Filed report "%s" into %s\'s folder.',
            $barangayLogDetail->item_description,
            $barangayLogDetail->barangay->barangay_name
        ));

        return back()->with('success', sprintf(
            'Report filed into %s\'s folder.',
            $barangayLogDetail->barangay->barangay_name
        ));
    }

    /**
     * The "Close" action on the Log Details list: marks the entry as
     * done/archived. Available from any status, since "close" here
     * just means "I'm finished with this one" rather than a judgement
     * about whether it was on time.
     */
    public function close(Request $request, BarangayLogDetail $barangayLogDetail): RedirectResponse
    {
        $barangayLogDetail->close($request->user()->name);

        AuditLog::record($request->user(), sprintf(
            'Closed barangay log entry "%s" (%s).',
            $barangayLogDetail->item_description,
            $barangayLogDetail->barangay->barangay_name
        ));

        return back()->with('success', 'Log entry closed.');
    }

    public function destroy(Request $request, BarangayLogDetail $barangayLogDetail): RedirectResponse
    {
        $description = sprintf('"%s" (%s)', $barangayLogDetail->item_description, $barangayLogDetail->barangay->barangay_name);

        $barangayLogDetail->delete();

        AuditLog::record($request->user(), sprintf('Deleted barangay log entry %s.', $description));

        return redirect()->route('barangay-log-details.index')
            ->with('success', 'Log entry deleted.');
    }

    private function validDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Exception) {
            return null;
        }
    }
}
