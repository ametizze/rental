<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Set tenant id
        // Resolve tenant id in priority order:
        // 1) route parameter (tenant or tenant_id)
        // 2) X-Tenant-ID header
        // 3) tenant_id query string
        // 4) authenticated user's tenant_id
        // 5) numeric subdomain (e.g. 123.example.com)
        // 6) fallback to 1
        $tenantId = $request->route('tenant_id') ?? $request->route('tenant') ?? $request->header('X-Tenant-ID') ?? $request->query('tenant_id');

        if (! $tenantId && auth()->check()) {
            $tenantId = auth()->user()->tenant_id ?? null;
        }

        if (! $tenantId) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            if (count($parts) > 2) {
                $subdomain = $parts[0];
                if (filter_var($subdomain, FILTER_VALIDATE_INT) !== false) {
                    $tenantId = (int) $subdomain;
                }
            }
        }

        // Ensure we have an integer tenant id (fallback to 1)
        $tenantId = filter_var($tenantId, FILTER_VALIDATE_INT) !== false ? (int) $tenantId : 1;

        if (! session()->has('tenant_id') || session('tenant_id') !== $tenantId) {
            session(['tenant_id' => $tenantId]);
        }

        return $next($request);
    }
}
