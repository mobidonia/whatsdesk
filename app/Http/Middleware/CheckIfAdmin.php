<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // let's check, if the current user is logged in
        if (Auth::check()) {

            // if he is logged in, check, if he is an admin
            if (Auth::user()->hasRole('admin')) {

                // if yes, he can pass...
                return $next($request);
            }

            // ... otherwise redirect him to another location of your choice...
            return redirect('/login');
        }

        // ... for example to the login-page
        return redirect('/login');
    }
}
