<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        // 1. Check user exists
        $user = \App\Models\Auth\User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'No account found with this email address.'])
                ->withInput($request->only('email'));
        }

        // 2. Check account is active
        if (!$user->is_active) {
            return back()
                ->withErrors(['email' => 'Your account has been deactivated. Please contact your administrator.'])
                ->withInput($request->only('email'));
        }

        // 3. Check if account is locked
        if ($user->isLocked()) {
            $remaining = now()->diffInMinutes($user->locked_until, false);
            return back()
                ->withErrors(['email' => "Account temporarily locked. Try again in {$remaining} minute(s)."])
                ->withInput($request->only('email'));
        }

        // 4. Attempt login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            $user->recordFailedLogin();

            $attemptsLeft = 5 - $user->fresh()->failed_attempts;

            if ($attemptsLeft <= 0) {
                return back()
                    ->withErrors(['email' => 'Too many failed attempts. Account locked for 15 minutes.'])
                    ->withInput($request->only('email'));
            }

            return back()
                ->withErrors(['password' => "Incorrect password. {$attemptsLeft} attempt(s) remaining."])
                ->withInput($request->only('email'));
        }

        // 5. Login success — reset failed attempts
        $request->session()->regenerate();

        $user->update([
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_login_at'   => now(),
        ]);

        // 6. Redirect based on role
        return match((int) $user->role_id) {
            1 => redirect()->intended('/admin/dashboard'),
            2 => redirect()->intended('/dashboard'),
            3 => redirect()->intended('/student-care/dashboard'),
            4 => redirect()->intended('/teacher/dashboard'),
            default => abort(403),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}