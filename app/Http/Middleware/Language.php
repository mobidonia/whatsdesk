<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Language
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeLanguage = session('applocale_change');
        if ($activeLanguage && $activeLanguage != 'android-chrome-256x256.png') {
            App::setLocale(strtolower($activeLanguage));
        } else {
        }

        return $next($request);
    }
}
