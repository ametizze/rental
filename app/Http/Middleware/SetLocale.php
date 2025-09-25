<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        // PRIORITY 1: Check Session (set via language selector)
        if ($request->hasSession() && $locale) {
            app()->setLocale($locale);
            return $next($request);
        }

        // PRIORITY 2: Check Database (if user is authenticated)
        if (auth()->check()) {
            $userLang = auth()->user()->preferred_lang;

            if ($userLang) {
                app()->setLocale($userLang);
                return $next($request);
            }
        }

        // PRIORITY 3: Fallback to Application Default (config/app.php)
        // O Laravel já faz isso, mas podemos ser explícitos:
        app()->setLocale(config('app.locale'));

        return $next($request);
    }
}
