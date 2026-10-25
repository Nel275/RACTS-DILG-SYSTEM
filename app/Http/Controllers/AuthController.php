<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public const PORTAL_DILG = 'dilg';
    public const PORTAL_BARANGAY = 'barangay';

    /**
     * Show the login form. `?portal=barangay` opens the Barangay tab.
     */
    public function showLoginForm(Request $request): View
    {
        $portal = $request->query('portal') === self::PORTAL_BARANGAY
            ? self::PORTAL_BARANGAY
            : self::PORTAL_DILG;

        return view('auth.login', compact('portal'));
    }

    /**
     * Handle a login attempt.
     *
     * The login form has two tabs - DILG Personnel and Barangay - so each
     * kind of user gets a form worded for them, and picking the wrong tab
     * gets a clear message instead of a confusing "wrong password". After
     * a successful login the account itself decides where the user lands
     * (a barangay account is classified to its own barangay automatically
     * from users.barangay_id - nothing is chosen by the user).
     */
    public function login(Request $request): RedirectResponse
    {
        $portal = $request->input('portal') === self::PORTAL_BARANGAY
            ? self::PORTAL_BARANGAY
            : self::PORTAL_DILG;

        $credentials = $request->validate([
            // Format check only - rejects markup/odd characters before they
            // ever reach the database. Length limits mirror ValidUsername.
            'username' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9._\-]+$/'],
            'password' => ['required', 'string', 'max:64'],
        ], [
            'username.regex' => 'Username may only contain letters, numbers, periods, underscores and hyphens.',
            'username.max' => 'Username is too long.',
            'password.max' => 'Password is too long.',
        ]);

        $throttleKey = strtolower($credentials['username']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors(['username' => "Too many failed attempts. Please try again in {$seconds} seconds."])
                ->withInput($request->only('username') + ['portal' => $portal]);
        }

        if (! Auth::validate($credentials)) {
            RateLimiter::hit($throttleKey, 60);

            return $this->failure($request, $portal, 'The username or password is incorrect.');
        }

        // Credentials are right - now check the account is allowed in and
        // that the right tab was used, before actually logging in.
        $user = User::where('username', $credentials['username'])->first();

        if (! $user->is_active) {
            return $this->failure($request, $portal, 'This account has been deactivated. Please contact the DILG administrator.');
        }

        if ($portal === self::PORTAL_BARANGAY && ! $user->isBarangay()) {
            return $this->failure($request, $portal, 'This is a DILG office account. Please use the "DILG Personnel" tab to sign in.');
        }

        if ($portal === self::PORTAL_DILG && $user->isBarangay()) {
            return $this->failure($request, $portal, 'This is a barangay account. Please use the "Barangay" tab to sign in.');
        }

        if ($user->isBarangay() && (! $user->barangay || ! $user->barangay->isActive())) {
            return $this->failure($request, $portal, 'Your account is not linked to an active barangay. Please contact the DILG office.');
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        AuditLog::record($user, $user->isBarangay()
            ? sprintf('Signed in (%s).', $user->barangay->barangay_name)
            : 'Signed in.');

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function failure(Request $request, string $portal, string $message): RedirectResponse
    {
        return back()
            ->withErrors(['username' => $message])
            ->withInput($request->only('username') + ['portal' => $portal]);
    }
}
