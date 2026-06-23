<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * Detects the tenant subdomain dynamically relative to the configured
     * APP_URL root domain. Works with any base domain:
     *   elections.com, elections.com.na, elections.nepticgroup.com, localhost, etc.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host       = $request->getHost();
        $rootDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? '';

        // On root domain or www.rootdomain — no tenant context
        if ($host === $rootDomain || $host === 'www.' . $rootDomain) {
            return $next($request);
        }

        // Only process subdomains of our own root domain
        if (!str_ends_with($host, '.' . $rootDomain)) {
            return $next($request);
        }

        // Extract everything to the left of .rootDomain
        $subdomain = substr($host, 0, -(strlen($rootDomain) + 1));

        if ($subdomain === '' || $subdomain === 'www') {
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
