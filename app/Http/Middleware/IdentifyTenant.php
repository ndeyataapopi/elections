<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use \App\Models\Tenant;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        $host = $request->getHost();

        // Split host
        $parts = explode('.', $host);

        // If no subdomain (elections.test), skip tenant logic
        if (count($parts) < 4) {
            return $next($request);
        }

        $subdomain = $parts[0];

        // Optional: skip www
        if ($subdomain === 'www') {
            return $next($request);
        }

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        app()->instance('currentTenant', $tenant);

        return $next($request);
    }
}
