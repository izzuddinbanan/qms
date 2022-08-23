<?php

namespace App\Http\Middleware;

use Closure;
use Entrust;

class IsAdmin
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
