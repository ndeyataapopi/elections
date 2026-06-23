<?php

use App\Models\Tenant;

if (!function_exists('tenant')) {
    /**
     * Return the currently identified tenant for this request,
     * or null when on the root (platform) domain.
     */
    function tenant(): ?Tenant
    {
        return app()->bound('currentTenant') ? app('currentTenant') : null;
    }
}
