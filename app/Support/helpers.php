<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Request;

/** Returns the current tenant ID (or null) */

use Illuminate\Support\Facades\DB;

function tenant_id(): ?int
{
    return session('tenant_id') ? (int) session('tenant_id') : null;
}

/** Tenant role */
function tenant_role(?int $userId = null, ?int $tenantId = null): ?string
{
    $uid = $userId ?? auth()->id();
    $tid = $tenantId ?? tenant_id();
    if (!$uid || !$tid) return null;

    return DB::table('user_tenants')
        ->where('user_id', $uid)
        ->where('tenant_id', $tid)
        ->value('role'); // returns null if not a member
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
