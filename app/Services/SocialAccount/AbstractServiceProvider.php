<?php

namespace App\Services\SocialAccount;

use Socialite;
use App\Entity\User;

abstract class AbstractServiceProvider
{
    /**
     * @var mixed
     */
    protected $provider;

    /**
     *  Create a new SocialServiceProvider instance
     */
    public function __construct()
    {
        $this->provider = Socialite::driver(
            str_replace(
                'serviceprovider', '', strtolower((new \ReflectionClass($this))->getShortName())
            )
        );
    }

    /**
     *  Logged in the user
     *
     *  @param  \App\Entity\User $user
     *  @return \Illuminate\Http\Response
     */
    protected function login($user)
    {
        if ($user->roles()->where('name', 'admin')->exists()) {
            auth()->login($user);
            return redirect()->intended('/');
        } else {
            request()->session()->flash('error-msg', 'This page is for admin!');
            return redirect('/login');
        }

    }

    /**
     *  Register the user
     *
     *  @param  array $input
     *  @return User $user
     */
    protected function register(array $input)
    {

        $user = User::create($input);

        return $user;
    }

    /**
     *  Redirect the user to provider authentication page
     *
     *  @return \Illuminate\Http\Response
     */
    public function redirect()
    {
        return $this->provider->redirect();
    }

    /**
     *  Handle data returned by the provider
     *
     *  @return \Illuminate\Http\Response
     */
    abstract public function handle();
}
