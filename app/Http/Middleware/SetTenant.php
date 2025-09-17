<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If no user is authenticated, just continue.
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // If the logged-in user is a superadmin, continue without setting a specific tenant.
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // If the user has a tenant_id, store the tenant in the Laravel service container.
        if ($user->tenant_id) {
            // Find the tenant and store it globally
            $tenant = Tenant::find($user->tenant_id);
            app()->instance('currentTenant', $tenant);
        }

        // Continue processing the request
        return $next($request);
    }
}
