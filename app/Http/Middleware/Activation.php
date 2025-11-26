<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Activation
{
    /**
     * Handle an incoming request.
     *
     * @return RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if(config('settings.is_demo',false)){
            //If it demo, it is oks
            return $next($request);
        }else if(config('settings.app_code_name','')=='reservations'){
            //If it reservations, it is ok
            return $next($request);
        }elseif ($this->alreadyActivated()) {
            //If it is activated, it is ok
            return $next($request);
        }else{
           //Return to verify.mobidonia.com
            return redirect()->to('https://verify.mobidonia.com/validate.php?url='.config('app.url'));
        }

        
    }

    /**
     * If application is already activated.
     */
    public function alreadyActivated(): bool
    {
        return file_exists(storage_path('activation'));
    }
}
