<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['en', 'es'];

        $locale = null;

        if (Auth::check() && in_array((string) Auth::user()->locale, $supported, true)) {
            $locale = Auth::user()->locale; // logged-in preference from DB
        } elseif (in_array((string) session('locale'), $supported, true)) {
            $locale = session('locale'); // guest/manual switch remembered in session
        } elseif (in_array((string) $request->cookie('locale'), $supported, true)) {
            $locale = $request->cookie('locale'); // remembered between visits
        } else {
            $locale = $request->getPreferredLanguage($supported) ?? config('app.locale'); // browser language
        }

        App::setLocale($locale);

        return $next($request);
    }
}
