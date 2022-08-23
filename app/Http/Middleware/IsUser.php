<?php

namespace App\Http\Middleware;

use Closure;
use Entrust;

class IsUser
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
        if (Entrust::hasRole('admin')) {
            return redirect()->route('home');
        }
        if (!Entrust::hasRole('user')) {
            return redirect()->to('/login')->send();
        }
        return $next($request);
    }
}
