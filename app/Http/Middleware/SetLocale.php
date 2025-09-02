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
    /** Uses ?lang=, then session, then Accept-Language; defaults to 'en'. */
    public function handle(Request $request, Closure $next)
    {
        $supported = ['en', 'pt_BR', 'es'];
        $lang = $request->query('lang')
            ?? session('lang')
            ?? $this->fromAcceptLanguage($request)
            ?? 'en';

        if (! in_array($lang, $supported, true)) $lang = 'en';

        app()->setLocale($lang);
        session(['lang' => $lang]);

        return $next($request);
    }

    private function fromAcceptLanguage(Request $request): ?string
    {
        $header = $request->header('Accept-Language', '');
        $header = strtolower($header);
        if (str_starts_with($header, 'pt-br')) return 'pt_BR';
        if (str_starts_with($header, 'es')) return 'es';
        if (str_starts_with($header, 'en')) return 'en';
        return null;
    }
}
