<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\BarangayLog;
use App\Models\BarangayLogDetail;
use App\Models\ReportRequirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Follow-up round: the Barangay Log Details list - built from the
 * "Purchases" page the client sent as a UI reference, remapped to this
 * app's own domain (Number -> Barangay, Supplier -> Report, with a
 * dropdown to pick the report/item type; Receiving Store, Total, and
 * Balance left out entirely, as asked). Lives as a second tab on the
 * existing Barangay Reports page - see report-grid/barangay.blade.php.
 *
 * Follow-up round: Report Filing is no longer Barangay-only - index()
 * and store() are now thin per-council wrappers (indexSk()/storeSk())
 * around a shared implementation, the same split ReportGridController
 * already uses for the Compliance Grid's barangay()/sk() methods. Every
 * other action here (show, markSubmitted, viewFile, download, close,
 * destroy) stays a single shared method - it's reached by a
 * BarangayLogDetail's own id, which already identifies which council
 * it belongs to via its barangayLog relation, so there's nothing for a
 * second, SK-specific route to add.
 */
class BarangayLogDetailController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listFor($request, ReportRequirement::COUNCIL_BARANGAY, 'barangay-log-details.index');
    }

    public function indexSk(Request $request): View
    {
        return $this->listFor($request, ReportRequirement::COUNCIL_SK, 'sk-log-details.index');
    }

    private function listFor(Request $request, string $councilType, string $viewName): View
    {
        // Self-healing status, the same idea as the automatic late-
        // submission checks used elsewhere in RACTS, just without a
        // separate scheduled command: any entry still "pending" whose
        // deadline has already passed is flipped to "missed" right
        // before the list is built, so the Status column and stat
        // cards are always accurate without anyone running anything.
        // Left unscoped by council - it's a harmless, idempotent sweep
        // either way, and scoping it would only add a join for no
        // behavioral difference.
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
            ->whereHas('barangayLog', fn ($q) => $q->where('council_type', $councilType))
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

        $reportOptions = BarangayLog::forCouncil($councilType)->orderBy('item_description')->get();
        $barangayOptions = Barangay::where('status', Barangay::STATUS_ACTIVE)->orderBy('barangay_name')->get();
        $activeLogItems = $reportOptions->where('status', BarangayLog::STATUS_ACTIVE);

        return view($viewName, compact(
            'entries',
            'stats',
            'status',
            'reportId',
            'search',
            'from',
            'to',
            'reportOptions',
            'barangayOptions',
            'activeLogItems',
            'councilType'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->storeFor($request, ReportRequirement::COUNCIL_BARANGAY, 'barangay-log-details.index');
    }

    public function storeSk(Request $request): RedirectResponse
    {
        return $this->storeFor($request, ReportRequirement::COUNCIL_SK, 'sk-log-details.index');
    }

    private function storeFor(Request $request, string $councilType, string $routeName): RedirectResponse
    {
        $validated = $request->validate([
            'barangay_id' => ['required', 'exists:barangays,id'],
            'item_description' => ['required', 'string', 'max:255'],
            'deadline_date' => ['required', 'date'],
        ]);

        $logItem = $this->findOrCreateLogItem(trim($validated['item_description']), $validated['deadline_date'], $councilType);

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
            'Added %s log entry "%s" for %s.',
            $logItem->councilLabel(),
            $entry->item_description,
            $entry->barangay->barangay_name
        ));

        return redirect()->route($routeName)
            ->with('success', 'Log entry added.');
    }

    /**
     * Follow-up round: Add Log Entry's Report field used to force you
     * over to "Manage Reports" first to add a brand-new report type
     * before you could log any barangay against it. It's now a single
     * step - type an existing report's name (autocomplete suggests the
     * active ones) to reuse it, or type a name that doesn't exist yet
     * and it's added to the report catalog on the spot, using this same
     * deadline. Matching is case/whitespace-insensitive and checks the
     * whole catalog (active and deactivated alike) so retyping an
     * existing name - even a deactivated one - never creates a
     * duplicate catalog entry.
     *
     * Follow-up round: the lookup is scoped to $councilType, so "Add
     * Log Entry" on the SK side can never silently reuse (or collide
     * with) a Barangay catalog item that happens to share the same
     * name, and vice versa - the two councils' report catalogs are
     * kept fully independent, same as their ReportRequirement slots.
     */
    private function findOrCreateLogItem(string $description, string $deadlineDate, string $councilType): BarangayLog
    {
        $existing = BarangayLog::forCouncil($councilType)
            ->whereRaw('LOWER(item_description) = ?', [strtolower($description)])
            ->first();

        if ($existing) {
            return $existing;
        }

        return BarangayLog::create([
            'item_description' => $description,
            'council_type' => $councilType,
            'deadline_date' => $deadlineDate,
            'status' => BarangayLog::STATUS_ACTIVE,
        ]);
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
     * Follow-up round: lets you open whatever's actually attached right
     * now, whether it's still staged or has already been filed into the
     * barangay's folder - report_file_path always points at the file's
     * real current location either way, so this works regardless of
     * status. Streamed inline (asks the browser to display it - a PDF
     * or image opens right in the tab; a Word doc still downloads,
     * since browsers have no built-in way to show one) rather than
     * forced as a download.
     *
     * The Content-Type is looked up from the file's own extension
     * instead of leaving Storage::response() to detect it from the
     * file's contents - some PHP installs (XAMPP's included one, in
     * particular) don't have the fileinfo extension enabled, which is
     * what that automatic detection depends on. Without it, the
     * browser isn't told the file is actually a PDF and falls back to
     * downloading it instead of opening its built-in viewer. Looking
     * the type up from the extension sidesteps that entirely.
     */
    public function viewFile(BarangayLogDetail $barangayLogDetail): StreamedResponse
    {
        abort_unless($barangayLogDetail->hasReportFile(), 404);
        abort_unless(Storage::disk('local')->exists($barangayLogDetail->report_file_path), 404);

        return Storage::disk('local')->response(
            $barangayLogDetail->report_file_path,
            $barangayLogDetail->report_original_name,
            ['Content-Type' => $this->reportFileMimeType($barangayLogDetail->report_original_name)]
        );
    }

    /**
     * Fallback mime-type lookup for viewFile() - see its doc comment
     * for why this doesn't rely on Storage's automatic detection.
     * Covers exactly the extensions the submit form already accepts
     * (see markSubmitted()'s validation above).
     */
    private function reportFileMimeType(?string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName ?? '', PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
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
        $routeName = $this->filingIndexRouteFor($barangayLogDetail);

        $barangayLogDetail->delete();

        AuditLog::record($request->user(), sprintf('Deleted barangay log entry %s.', $description));

        return redirect()->route($routeName)
            ->with('success', 'Log entry deleted.');
    }

    /**
     * Which council's Report Filing list an entry belongs to, so an
     * action on an SK entry never redirects back to the Barangay list
     * (or vice versa). Resolved from the entry's barangayLog relation -
     * see BarangayLog's own doc comment on council_type.
     */
    private function filingIndexRouteFor(BarangayLogDetail $barangayLogDetail): string
    {
        return $barangayLogDetail->barangayLog->council_type === ReportRequirement::COUNCIL_SK
            ? 'sk-log-details.index'
            : 'barangay-log-details.index';
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
