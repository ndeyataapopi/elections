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
        $request->authenticate();

        $user = Auth::user();
        $currentTenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        // Check 1: If on main domain (no subdomain) - only super_admin allowed
        if (!$currentTenant) {
            if (!$user->isSuperAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'You are not authorized to access the platform admin area.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('platform.dashboard', absolute: false));
        }

        // Check 2: If on tenant subdomain - user must belong to this tenant
        if ($currentTenant) {
            // Super admin can access any tenant subdomain
            if ($user->isSuperAdmin()) {
                $request->session()->regenerate();
                return redirect()->intended(route('tenant.dashboard', absolute: false));
            }

            // Regular users can only access their own tenant
            if ($user->tenant_id !== $currentTenant->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'You are not authorized to access this tenant. Please use your own tenant URL.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('tenant.dashboard', absolute: false));
        }

        $request->session()->regenerate();
        return redirect()->intended(route('platform.dashboard', absolute: false));
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
