<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Follow-up round: User Logs - a barangay's personnel/officials roster
 * (client's reference table `userslogs`). Both roles can view; only the
 * administrator can add, edit, or delete entries, matching every other
 * management page in RACTS. A personnel record can optionally be given a
 * real RACTS login account (DILG Administrator or DILG Monitoring Staff
 * role only) - see UserLog's own doc comment for how that link works.
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

        $createAccount = $request->boolean('create_account');
        $accountData = $createAccount ? $this->validateAccount($request) : null;

        $log = DB::transaction(function () use ($validated, $accountData) {
            if ($accountData) {
                $account = User::create([
                    'name' => trim(sprintf('%s %s', $validated['first_name'], $validated['last_name'])),
                    'username' => $accountData['username'],
                    'password' => $accountData['password'],
                    'role' => $accountData['account_role'],
                ]);

                $validated['user_id'] = $account->id;
            }

            return UserLog::create($validated);
        });

        $message = $accountData
            ? sprintf('Added user log "%s" with a login account.', $log->fullName())
            : sprintf('Added user log "%s".', $log->fullName());
        AuditLog::record($request->user(), $message);

        return redirect()->route('user-logs.index')->with('success', 'Personnel record added.');
    }

    public function show(UserLog $userLog): View
    {
        $userLog->load('barangay', 'account');

        return view('user-logs.show', compact('userLog'));
    }

    public function edit(UserLog $userLog): View
    {
        $userLog->load('account');
        $barangayOptions = Barangay::where('status', Barangay::STATUS_ACTIVE)->orderBy('barangay_name')->get();

        return view('user-logs.edit', compact('userLog', 'barangayOptions'));
    }

    public function update(Request $request, UserLog $userLog): RedirectResponse
    {
        $validated = $this->validateUserLog($request);
        $validated['date_process'] = now();
        $validated['processed_by'] = $request->user()->name;

        $hadAccount = $userLog->hasLoginAccount();
        $willCreateAccount = ! $hadAccount && $request->boolean('create_account');

        $accountData = null;
        if ($hadAccount) {
            // Already linked: account fields are always editable, but a
            // blank password means "keep the current one" - only
            // required when they're setting one for the first time.
            $accountData = $this->validateAccount($request, $userLog->account, passwordRequired: false);
        } elseif ($willCreateAccount) {
            $accountData = $this->validateAccount($request);
        }

        DB::transaction(function () use ($userLog, $validated, $accountData, $hadAccount, $willCreateAccount) {
            if ($hadAccount && $accountData) {
                $accountUpdate = [
                    'username' => $accountData['username'],
                    'role' => $accountData['account_role'],
                ];
                if (! empty($accountData['password'])) {
                    $accountUpdate['password'] = $accountData['password'];
                }
                $userLog->account->update($accountUpdate);
            } elseif ($willCreateAccount && $accountData) {
                $account = User::create([
                    'name' => trim(sprintf('%s %s', $validated['first_name'], $validated['last_name'])),
                    'username' => $accountData['username'],
                    'password' => $accountData['password'],
                    'role' => $accountData['account_role'],
                ]);
                $validated['user_id'] = $account->id;
            }

            $userLog->update($validated);
        });

        AuditLog::record($request->user(), sprintf('Updated user log "%s".', $userLog->fullName()));

        return redirect()->route('user-logs.index')->with('success', 'Personnel record updated.');
    }

    public function destroy(Request $request, UserLog $userLog): RedirectResponse
    {
        if ($userLog->hasLoginAccount()) {
            return redirect()->route('user-logs.index')
                ->with('error', 'Can\'t delete "'.$userLog->fullName().'" - they have a login account. Remove the account from Users first, or set this record to Inactive instead.');
        }

        $name = $userLog->fullName();

        $userLog->delete();

        AuditLog::record($request->user(), sprintf('Deleted user log "%s".', $name));

        return redirect()->route('user-logs.index')->with('success', 'Personnel record deleted.');
    }

    private function validateUserLog(Request $request): array
    {
        // Tidy spacing first so "  Juan   Dela Cruz " is checked and saved as "Juan Dela Cruz".
        $request->merge(collect(['first_name', 'middle_name', 'last_name', 'designation', 'roles'])
            ->mapWithKeys(fn ($field) => [$field => $request->filled($field)
                ? trim(preg_replace('/\s+/u', ' ', (string) $request->input($field)))
                : null])
            ->all());

        // Letters (incl. Ñ/accents), spaces, periods, apostrophes and hyphens -
        // e.g. "Ma. Cristina", "Dela Cruz-Santos", "O'Neil" - and at least one letter.
        $text = 'regex:/^(?=.*\pL)[\pL .\'\-]+$/u';

        return $request->validate([
            'first_name' => ['required', 'string', 'max:100', $text],
            'middle_name' => ['nullable', 'string', 'max:100', $text],
            'last_name' => ['required', 'string', 'max:100', $text],
            'birthdate' => ['required', 'date', 'after:1900-01-01', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(UserLog::GENDERS)],
            'designation' => ['required', 'string', 'max:100', $text],
            'roles' => ['required', 'string', 'max:100', $text],
            'status' => ['nullable', Rule::in(array_keys(UserLog::STATUSES))],
            'barangay_id' => ['nullable', 'exists:barangays,id'],
        ], [
            'first_name.regex' => 'First name may only contain letters, spaces, periods, apostrophes and hyphens.',
            'middle_name.regex' => 'Middle name may only contain letters, spaces, periods, apostrophes and hyphens.',
            'last_name.regex' => 'Last name may only contain letters, spaces, periods, apostrophes and hyphens.',
            'designation.regex' => 'Designation may only contain letters, spaces, periods, apostrophes and hyphens.',
            'roles.regex' => 'Role may only contain letters, spaces, periods, apostrophes and hyphens.',
            'birthdate.before_or_equal' => 'Birthdate cannot be in the future.',
            'gender.in' => 'Please select a gender.',
        ]);
    }

    /**
     * Validates the Login Account fields on the User Logs form. Role is
     * restricted to DILG Administrator / DILG Monitoring Staff only -
     * there's no third option for a barangay personnel account.
     */
    private function validateAccount(Request $request, ?User $existingUser = null, bool $passwordRequired = true): array
    {
        return $request->validate([
            'username' => [
                'required', 'string', 'min:4', 'max:50', 'regex:/^[A-Za-z0-9._]+$/',
                Rule::unique('users', 'username')->ignore($existingUser?->id),
            ],
            'password' => $passwordRequired
                ? ['required', 'string', 'min:8', 'confirmed']
                : ['nullable', 'string', 'min:8', 'confirmed'],
            'account_role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_STAFF])],
        ], [
            'username.regex' => 'Username may only contain letters, numbers, periods and underscores (no spaces).',
        ]);
    }
}
