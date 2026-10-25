<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Rules\ValidPersonName;
use App\Rules\ValidUsername;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * User account management - DILG Administrator only.
 *
 * Panelist revision: accounts now come in three kinds (DILG Administrator,
 * DILG Monitoring Staff, Barangay User). A barangay account must be linked
 * to a barangay - that link is how the system "classifies" the user to
 * their barangay automatically at login. Accounts can be deactivated
 * instead of deleted, and the system refuses any change that would leave
 * it without an active administrator or lock an admin out of their own
 * account.
 */
class UserController extends Controller
{
    /**
     * Display a listing of user accounts, filterable by role/status.
     */
    public function index(Request $request): View
    {
        $role = $request->string('role')->trim()->value();
        $search = $request->string('search')->trim()->value();

        $users = User::with('barangay')
            ->when(array_key_exists($role, User::ROLES), fn ($q) => $q->where('role', $role))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('username', 'like', "%{$search}%"));
            })
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'role', 'search'));
    }

    /**
     * Show the form for creating a new user account.
     */
    public function create(): View
    {
        return view('users.create', ['barangays' => $this->barangayOptions()]);
    }

    /**
     * Store a newly created user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $user = User::create($validated);

        AuditLog::record($request->user(), sprintf(
            'Created user account "%s" (%s, username: %s).',
            $user->name,
            $user->roleLabel(),
            $user->username
        ));

        return redirect()
            ->route('users.index')
            ->with('success', 'User account created successfully.');
    }

    /**
     * Show the form for editing a user account.
     */
    public function edit(User $user): View
    {
        return view('users.edit', ['user' => $user, 'barangays' => $this->barangayOptions()]);
    }

    /**
     * Update a user account.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validated($request, $user);

        // Leave the existing password untouched if the field was left blank.
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $isSelf = $user->id === $request->user()->id;
        $losesAdmin = $user->isAdmin()
            && ($validated['role'] !== User::ROLE_ADMIN || ! $validated['is_active']);

        if ($losesAdmin && $isSelf) {
            return back()->withInput()->with('error', 'You cannot change your own role or deactivate your own account.');
        }

        if ($losesAdmin && $this->isLastActiveAdmin($user)) {
            return back()->withInput()->with('error', 'This is the last active administrator. Make another account an administrator first.');
        }

        $user->update($validated);

        AuditLog::record($request->user(), sprintf(
            'Updated user account "%s" (%s, username: %s, %s).',
            $user->name,
            $user->roleLabel(),
            $user->username,
            $user->is_active ? 'active' : 'deactivated'
        ));

        return redirect()
            ->route('users.index')
            ->with('success', 'User account updated successfully.');
    }

    /**
     * Remove a user account.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account while logged in.');
        }

        if ($user->isAdmin() && $this->isLastActiveAdmin($user)) {
            return back()->with('error', 'This is the last active administrator and cannot be deleted.');
        }

        $deletedName = $user->name;
        $deletedUsername = $user->username;

        $user->delete();

        AuditLog::record($request->user(), sprintf(
            'Deleted user account "%s" (username: %s).',
            $deletedName,
            $deletedUsername
        ));

        return redirect()
            ->route('users.index')
            ->with('success', 'User account deleted successfully.');
    }

    /**
     * Shared create/update validation. Normalizes spacing first, applies
     * the character and inappropriate-word filters (App\Rules), and
     * ties barangay_id to the role: required for a barangay account,
     * always cleared for DILG accounts.
     */
    private function validated(Request $request, ?User $existing = null): array
    {
        $request->merge([
            'name' => trim(preg_replace('/\s+/u', ' ', (string) $request->input('name'))),
        ]);

        $isBarangay = $request->input('role') === User::ROLE_BARANGAY;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', new ValidPersonName],
            'username' => ['required', 'string', 'max:30', new ValidUsername, Rule::unique('users', 'username')->ignore($existing?->id)],
            'password' => [
                $existing ? 'nullable' : 'required', 'string', 'confirmed',
                new StrongPassword($request->input('username')),
            ],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'barangay_id' => [
                Rule::requiredIf($isBarangay), 'nullable',
                Rule::exists('barangays', 'id')->where('status', Barangay::STATUS_ACTIVE),
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'barangay_id.required' => 'Please choose the barangay this account belongs to.',
            'barangay_id.exists' => 'Please choose an active barangay.',
        ]);

        if (! $isBarangay) {
            $validated['barangay_id'] = null;
        }

        // A checkbox is absent from the request when unticked; on create the
        // account is always active.
        $validated['is_active'] = $existing ? $request->boolean('is_active') : true;

        return $validated;
    }

    private function isLastActiveAdmin(User $user): bool
    {
        return $user->is_active
            && User::activeAdmins()->where('id', '!=', $user->id)->doesntExist();
    }

    private function barangayOptions()
    {
        return Barangay::where('status', Barangay::STATUS_ACTIVE)->orderBy('barangay_name')->get();
    }
}
