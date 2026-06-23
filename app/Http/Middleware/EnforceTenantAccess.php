<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnforceTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentTenant = tenant();

        // Check 1: If on main domain (no subdomain) - only super_admin allowed
        if (!$currentTenant) {
            if (!$user->isSuperAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'email' => 'You are not authorized to access the platform admin area.',
                ]);
            }

            return $next($request);
        }

        // Check 2: If on tenant subdomain - user must belong to this tenant
        if ($currentTenant) {
            // Super admin can access any tenant subdomain
            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // Regular users can only access their own tenant
            if ($user->tenant_id !== $currentTenant->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'email' => 'You are not authorized to access this tenant. Please use your own tenant URL.',
                ]);
            }

            return $next($request);
        }

        return $next($request);
    }
}
