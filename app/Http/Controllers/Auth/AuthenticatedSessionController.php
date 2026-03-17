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

        $user = \App\Models\Auth\User::where('email', $credentials['email'])
                ->with('role.permissions')
                ->first();
                
        if (!$user) {
            return back()->withErrors([
                'email' => 'Invalid credentials.'
            ]);
        }

        // check if account active
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive.'
            ]);
        }

        // check if account locked
        if ($user->isLocked()) {
            return back()->withErrors([
                'email' => 'Account locked. Try again later.'
            ]);
        }

        // attempt login
        if (!Auth::attempt($credentials)) {

            // record failed attempt
            $user->recordFailedLogin();

            return back()
                ->withErrors(['email' => 'Invalid credentials.'])
                ->withInput($request->only('email'));
        }

        // login success
        $request->session()->regenerate();
        $user->load('role.permissions');

        // reset attempts
        $user->update([
            'failed_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now()
        ]);

        return redirect()->intended(route('dashboard'));
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
