<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BarangayLogDetail;
use App\Models\ReportStamp;
use App\Services\ReportStamper;
use App\Services\StampingException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Digital Stamping - DILG Administrator only (every route here is behind
 * role:admin, and ReportStamper re-checks the role itself). Lists the
 * reports submitted to DILG, applies stamps, and serves stamped copies.
 * verify() is the exception: any signed-in user may check a code.
 */
class DigitalStampingController extends Controller
{
    public const STATES = ['awaiting' => 'Awaiting stamp', 'stamped' => 'Stamped', 'all' => 'All submitted'];

    public function index(Request $request): View
    {
        $state = array_key_exists($request->query('state'), self::STATES) ? $request->query('state') : 'awaiting';
        $currentStamp = fn ($q) => $q->whereColumn('report_stamps.original_file_hash', 'barangay_log_details.report_file_hash');

        $entries = BarangayLogDetail::with(['barangay', 'barangayLog', 'stamps'])
            ->whereNotNull('date_submitted')
            ->whereNotNull('report_file_path')
            ->whereIn('status', BarangayLogDetail::SUBMITTED_STATUSES)
            ->when($state === 'awaiting', fn ($q) => $q->whereDoesntHave('stamps', $currentStamp))
            ->when($state === 'stamped', fn ($q) => $q->whereHas('stamps', $currentStamp))
            ->orderByDesc('date_submitted')
            ->paginate(15)
            ->withQueryString();

        return view('digital-stamping.index', compact('entries', 'state'));
    }

    public function show(BarangayLogDetail $barangayLogDetail): View
    {
        $barangayLogDetail->load(['barangay', 'barangayLog', 'stamps.user']);

        return view('digital-stamping.show', ['entry' => $barangayLogDetail]);
    }

    public function stamp(Request $request, BarangayLogDetail $barangayLogDetail, ReportStamper $stamper): RedirectResponse
    {
        $user = $request->user();
        $ref = $barangayLogDetail->referenceNumber();

        try {
            $stamp = $stamper->stamp($barangayLogDetail, $user);
        } catch (StampingException $e) {
            if ($e->getPrevious()) {
                Log::error('Report stamping failed', ['entry' => $barangayLogDetail->id, 'exception' => $e->getPrevious()]);
            }
            AuditLog::record($user, sprintf('Stamping FAILED for report %s "%s": %s', $ref, $barangayLogDetail->item_description, $e->getMessage()));

            return back()->with('error', $e->getMessage());
        }

        if (! $stamp->wasRecentlyCreated) {
            return redirect()->route('digital-stamping.show', $barangayLogDetail)
                ->with('error', sprintf('This file was already stamped on %s by %s - no new stamp was created.',
                    $stamp->created_at->format('M d, Y g:i A'), $stamp->stamped_by_name));
        }

        AuditLog::record($user, sprintf(
            'Stamped report %s "%s" (%s) - verification code %s, original sha256 %s, stamped sha256 %s.',
            $ref,
            $barangayLogDetail->item_description,
            $barangayLogDetail->barangay->barangay_name,
            $stamp->verification_code,
            $stamp->original_file_hash,
            $stamp->stamped_file_hash
        ));

        return redirect()->route('digital-stamping.show', $barangayLogDetail)
            ->with('success', 'Report stamped. The stamped copy is ready to download.');
    }

    public function download(ReportStamp $reportStamp): StreamedResponse
    {
        return self::streamStamp($reportStamp);
    }

    /**
     * Stamp verification by code: answers only from server-side records,
     * and shows no report contents or file links.
     */
    public function verify(Request $request): View
    {
        $code = strtoupper($request->string('code')->trim()->substr(0, 32)->value());
        $stamp = $code === '' ? null : ReportStamp::with('barangayLogDetail.barangay')->where('verification_code', $code)->first();

        return view('digital-stamping.verify', compact('code', 'stamp'));
    }

    /** Shared with BarangayPortalController so both serve stamps the same way. */
    public static function streamStamp(ReportStamp $stamp): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($stamp->stamped_file_path), 404);

        return Storage::disk('local')->download($stamp->stamped_file_path, $stamp->stamped_original_name, [
            'Content-Type' => str_ends_with($stamp->stamped_file_path, '.pdf')
                ? 'application/pdf'
                : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
