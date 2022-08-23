<?php

namespace App\Http\Middleware;

use Entrust;
use Closure;

class IsSuperUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        if (!Entrust::hasRole('super_user')) {
            return redirect()->to('/login')->send();
        }

        return $next($request);
    }
}
