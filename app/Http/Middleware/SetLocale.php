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
        // Set locale from session
        if ($request->session()->has('locale')) {
            app()->setLocale($request->session()->get('locale'));
        }
        // Or from authenticated user
        elseif (auth()->check()) {
            $locale = auth()->user()->locale ?? 'en';
            $request->session()->put('locale', $locale);
            app()->setLocale($locale);
        }
        // Default to English
        else {
            app()->setLocale('en');
        }

        return $next($request);
    }
}
