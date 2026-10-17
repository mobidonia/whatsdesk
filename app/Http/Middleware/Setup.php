<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Setup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->alreadyInstalled() && explode('/', $request->route() ? $request->route()->uri() : '')[0] !== 'install') {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }

    /**
     * If application is already installed.
     */
    public function alreadyInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }
}
