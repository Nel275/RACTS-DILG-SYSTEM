<?php

namespace App\Http\Controllers;

use App\Rules\NoInappropriateWords;
use App\Models\AuditLog;
use App\Models\Barangay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BarangayController extends Controller
{
    /**
     * Display a listing of barangays, with optional search and status filter.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();

        $barangays = Barangay::query()
            ->when($search, function ($query, $search) {
                $query->where('barangay_name', 'like', "%{$search}%");
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('barangay_name')
            ->paginate(10)
            ->withQueryString();

        // Follow-up round: small summary cards at the top of the page,
        // matching the visual pattern used for the new Barangay Log
        // Details list. Counted against the whole table, not just the
        // current page/filter, so they read as a stable total.
        $stats = [
            'total' => Barangay::count(),
            'active' => Barangay::where('status', Barangay::STATUS_ACTIVE)->count(),
            'inactive' => Barangay::where('status', Barangay::STATUS_INACTIVE)->count(),
        ];

        return view('barangays.index', compact('barangays', 'search', 'status', 'stats'));
    }

    /**
     * Show the form for creating a new barangay.
     */
    public function create(): View
    {
        return view('barangays.create');
    }

    /**
     * Store a newly created barangay.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBarangay($request);
        $validated['date_process'] = now();
        $validated['processed_by'] = $request->user()->name;

        $barangay = Barangay::create($validated);

        // Follow-up round: every barangay gets its own storage folder
        // the moment it's created - this is where Barangay Log Details
        // files reports into once "Download" is used on them.
        $barangay->ensureFolder();

        AuditLog::record($request->user(), sprintf('Added barangay "%s".', $barangay->barangay_name));

        return redirect()
            ->route('barangays.index')
            ->with('success', 'Barangay added successfully.');
    }

    /**
     * Display a single barangay's details.
     */
    public function show(Barangay $barangay): View
    {
        return view('barangays.show', compact('barangay'));
    }

    /**
     * Show the form for editing a barangay.
     */
    public function edit(Barangay $barangay): View
    {
        return view('barangays.edit', compact('barangay'));
    }

    /**
     * Update a barangay.
     */
    public function update(Request $request, Barangay $barangay): RedirectResponse
    {
        $previousName = $barangay->barangay_name;

        $validated = $this->validateBarangay($request, $barangay);
        $validated['date_process'] = now();
        $validated['processed_by'] = $request->user()->name;

        $barangay->update($validated);

        // Follow-up round: the storage folder is now named after the
        // barangay itself, so a rename here needs to carry that folder
        // (and every already-filed report's stored path) along with it.
        if ($barangay->barangay_name !== $previousName) {
            $barangay->moveFolderForRename($previousName);
        }

        AuditLog::record($request->user(), sprintf(
            'Updated barangay "%s" (status: %s).',
            $barangay->barangay_name,
            $barangay->statusLabel()
        ));

        return redirect()
            ->route('barangays.index')
            ->with('success', 'Barangay updated successfully.');
    }

    /**
     * Remove a barangay.
     *
     * Takes $request now (didn't before this round) purely to identify
     * who's performing the deletion for the audit log below - nothing
     * else about this method changed.
     */
    public function destroy(Request $request, Barangay $barangay): RedirectResponse
    {
        if ($barangay->users()->exists()) {
            return back()->with(
                'error',
                'This barangay cannot be deleted because it still has user accounts linked to it.'
            );
        }

        if ($barangay->reportRecords()->exists()) {
            return back()->with(
                'error',
                'This barangay cannot be deleted because it has submission history recorded against it.'
            );
        }

        if ($barangay->logDetails()->exists()) {
            return back()->with(
                'error',
                'This barangay cannot be deleted because it has Barangay Log Details entries recorded against it.'
            );
        }

        $deletedName = $barangay->barangay_name;

        $barangay->delete();

        AuditLog::record($request->user(), sprintf('Deleted barangay "%s".', $deletedName));

        return redirect()
            ->route('barangays.index')
            ->with('success', 'Barangay deleted successfully.');
    }

    /**
     * Shared validation rules for store/update.
     *
     * Follow-up round: barangay_name must now be unique. The storage
     * folder is named after it (Barangay::storagePath()), so two
     * barangays sharing a name would otherwise collide into the same
     * folder - this also just matches how a real DILG office would
     * expect barangay names to work.
     */
    private function validateBarangay(Request $request, ?Barangay $barangay = null): array
    {
        // Trim and collapse repeated spaces first, so "  Centro   West " is
        // checked (and saved) as "Centro West".
        $request->merge([
            'barangay_name' => trim(preg_replace('/\s+/u', ' ', (string) $request->input('barangay_name'))),
        ]);

        return $request->validate([
            'barangay_name' => [
                'required', 'string', 'max:255',
                // Letters (including Ñ and other accented letters), spaces,
                // periods and hyphens only - and at least one letter.
                'regex:/^(?=.*\pL)[\pL .\-]+$/u', new NoInappropriateWords,
                Rule::unique('barangays', 'barangay_name')->ignore($barangay?->id),
            ],
            'status' => ['required', Rule::in(array_keys(Barangay::STATUSES))],
        ], [
            'barangay_name.regex' => 'The barangay name may only contain letters, spaces, periods (.) and hyphens (-).',
        ]);
    }
}
