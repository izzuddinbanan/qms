<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Session;
use Entrust;
use App\Entity\User;
use App\Entity\RoleUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function attemptLogin(Request $request)
    {

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            
            if(!Auth::user()->hasRole('super_user') && !Auth::user()->hasRole('power_user') && !Auth::user()->hasRole('admin')){
                $this->logout($request);
            }
            
            $role_user = RoleUser::where('user_id', Auth::user()->id)->first();
            Session::put('role_user_id', $role_user->id);
            
        }

        
    }
}
