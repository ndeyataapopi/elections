<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * The 'currentTenant' binding is NOT pre-registered here — it is resolved
     * lazily by IdentifyTenant middleware on each request. Use the tenant()
     * global helper (app/helpers.php) to read it safely anywhere in the app.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
