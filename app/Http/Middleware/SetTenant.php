<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    public function handle(Request $request, Closure $next)
    {
        // prioridade: rota > header > query > auth > sessão atual
        $route     = $request->route();
        $candidate = $route?->parameter('tenant_id')
            ?? $route?->parameter('tenant')
            ?? $request->header('X-Tenant-ID')
            ?? $request->header('X-Tenant')
            ?? $request->query('tenant_id')
            ?? $request->query('tenant')
            ?? (auth()->check() ? (auth()->user()->tenant_id ?? null) : null)
            ?? session('tenant_id');

        $id = $this->toInt($candidate); // só aceita números; nada de slug/subdomínio

        if (!is_null($id)) {
            session(['tenant_id' => $id]);
        }

        return $next($request);
    }

    private function toInt($value): ?int
    {
        return (is_int($value) || (is_string($value) && ctype_digit($value)))
            ? (int) $value
            : null;
    }
}
