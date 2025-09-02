<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Request;

/** Returns the current tenant ID (or null) */
function tenant_id(): ?int
{
    return session('tenant_id') ? (int) session('tenant_id') : null;
}

/** Returns the current tenant model (lightweight cache per request) */
function tenant(): ?Tenant
{
    static $cached = false;
    static $tenant = null;

    if ($cached) return $tenant;

    $id = tenant_id();
    $tenant = $id ? Tenant::find($id) : null;
    $cached = true;

    return $tenant;
}
