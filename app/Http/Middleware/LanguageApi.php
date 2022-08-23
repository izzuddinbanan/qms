<?php

namespace App\Http\Middleware;

use Closure;
use App\Entity\Language;

class LanguageApi
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
        
        $lang_short = Language::find(\Auth::user()->language_id);
        
        app()->setLocale($lang_short->abbreviation_name);

        return $next($request);
    }
}
