<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Rules\PlainText;
use App\Rules\StrongPassword;
use App\Rules\ValidPersonName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Panelist revision: a personalized profile for every account type.
 * Users can edit their own display name, contact number, position and
 * photo, and change their own password. Username and role stay under
 * the administrator's control (Users page) - a user must not be able to
 * promote themselves or take someone else's username.
 */
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load('barangay');

        return view('profile.show', [
            'user' => $user,
            'permissions' => $user->permissions(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', new ValidPersonName],
            'position' => ['nullable', 'string', 'max:100', new PlainText],
            // PH mobile or landline, with optional +63 / dashes / spaces.
            'contact_number' => ['nullable', 'string', 'max:20', 'regex:/^(\+?63|0)[0-9 \-()]{8,14}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'contact_number.regex' => 'Enter a valid Philippine phone number (e.g. 09171234567 or +63 917 123 4567).',
            'avatar.image' => 'The photo must be a JPG or PNG image.',
            'avatar.max' => 'The photo must be 2 MB or smaller.',
        ]);

        $attributes = [
            'name' => $validated['name'],
            'position' => $validated['position'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('local')->delete($user->avatar_path);
            }
            $attributes['avatar_path'] = $request->file('avatar')->store('avatars', 'local');
        } elseif ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('local')->delete($user->avatar_path);
            $attributes['avatar_path'] = null;
        }

        $user->update($attributes);

        AuditLog::record($user, 'Updated their profile.');

        return redirect()->route('profile.show')->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', new StrongPassword($user->username)],
        ]);

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        if (Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'Your new password must be different from the current one.']);
        }

        $user->update(['password' => $request->input('password')]);

        AuditLog::record($user, 'Changed their password.');

        return redirect()->route('profile.show')->with('success', 'Password changed.');
    }

    /**
     * Streams a user's profile photo. Photos live on the private disk
     * (the app has no public/storage link), so they are served through
     * this login-protected route rather than a public URL.
     */
    public function avatar(User $user): StreamedResponse
    {
        abort_unless($user->avatar_path && Storage::disk('local')->exists($user->avatar_path), 404);

        return Storage::disk('local')->response($user->avatar_path, null, [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
