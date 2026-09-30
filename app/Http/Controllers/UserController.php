<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the DILG user accounts.
     */
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user account.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

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
        return view('users.edit', compact('user'));
    }

    /**
     * Update a user account.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        // Leave the existing password untouched if the field was left blank.
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        AuditLog::record($request->user(), sprintf(
            'Updated user account "%s" (%s, username: %s).',
            $user->name,
            $user->roleLabel(),
            $user->username
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
}
