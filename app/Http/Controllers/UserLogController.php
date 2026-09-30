<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\UserLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Follow-up round: User Logs - a barangay's personnel/officials roster
 * (client's reference table `userslogs`). Not related to real login
 * accounts - see UserLog's own doc comment. Both roles can view; only
 * the administrator can add, edit, or delete entries, matching every
 * other management page in RACTS.
 */
class UserLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();
        $barangayId = $request->integer('barangay_id') ?: null;

        $logs = UserLog::query()
            ->with('barangay')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($barangayId, fn ($query, $barangayId) => $query->where('barangay_id', $barangayId))
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => UserLog::count(),
            'active' => UserLog::where('status', UserLog::STATUS_ACTIVE)->count(),
            'inactive' => UserLog::where('status', UserLog::STATUS_INACTIVE)->count(),
        ];

        $barangayOptions = Barangay::orderBy('barangay_name')->get();

        return view('user-logs.index', compact('logs', 'search', 'status', 'barangayId', 'stats', 'barangayOptions'));
    }

    public function create(): View
    {
        $barangayOptions = Barangay::where('status', Barangay::STATUS_ACTIVE)->orderBy('barangay_name')->get();

        return view('user-logs.create', compact('barangayOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUserLog($request);
        $validated['date_process'] = now();
        $validated['processed_by'] = $request->user()->name;
        $validated['status'] = $validated['status'] ?? UserLog::STATUS_ACTIVE;

        $log = UserLog::create($validated);

        AuditLog::record($request->user(), sprintf('Added user log "%s".', $log->fullName()));

        return redirect()->route('user-logs.index')->with('success', 'Personnel record added.');
    }

    public function show(UserLog $userLog): View
    {
        $userLog->load('barangay');

        return view('user-logs.show', compact('userLog'));
    }

    public function edit(UserLog $userLog): View
    {
        $barangayOptions = Barangay::where('status', Barangay::STATUS_ACTIVE)->orderBy('barangay_name')->get();

        return view('user-logs.edit', compact('userLog', 'barangayOptions'));
    }

    public function update(Request $request, UserLog $userLog): RedirectResponse
    {
        $validated = $this->validateUserLog($request);
        $validated['date_process'] = now();
        $validated['processed_by'] = $request->user()->name;

        $userLog->update($validated);

        AuditLog::record($request->user(), sprintf('Updated user log "%s".', $userLog->fullName()));

        return redirect()->route('user-logs.index')->with('success', 'Personnel record updated.');
    }

    public function destroy(Request $request, UserLog $userLog): RedirectResponse
    {
        $name = $userLog->fullName();

        $userLog->delete();

        AuditLog::record($request->user(), sprintf('Deleted user log "%s".', $name));

        return redirect()->route('user-logs.index')->with('success', 'Personnel record deleted.');
    }

    private function validateUserLog(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:150'],
            'middle_name' => ['nullable', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            'birthdate' => ['required', 'date'],
            'gender' => ['required', 'string', 'max:150'],
            'designation' => ['required', 'string', 'max:150'],
            'roles' => ['required', 'string', 'max:150'],
            'status' => ['nullable', Rule::in(array_keys(UserLog::STATUSES))],
            'barangay_id' => ['nullable', 'exists:barangays,id'],
        ]);
    }
}
