<?php

namespace App\Http\Middleware;

use App\Models\Company as ModelsCompany;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class checkActiveCompany
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tmp = explode('/', URL::current());
        $alias = end($tmp);
        $company = ModelsCompany::where('subdomain', $alias)->first();

        if ($company->active == 1) {
            return $next($request);
        } else {
            return redirect('/');
        }
    }
}
