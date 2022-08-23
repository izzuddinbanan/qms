<?php

namespace App\Http\Middleware;

use Closure, Session, Auth;
use App\Entity\Language as LangDB;
class Language
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
        if(Auth::user()){

            $user = Auth::user();

            $lang_short = LangDB::find($user->language_id);

            app()->setLocale($lang_short->abbreviation_name);

        }else{
            
            app()->setLocale('en');
        }

        return $next($request);
    }
}
