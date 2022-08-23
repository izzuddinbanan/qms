<?php

namespace App\Services\SocialAccount;

use App\Entity\User;
use App\Entity\SocialAccount;
use Laravel\Socialite\Contracts\User as ProviderUser;

class GoogleServiceProvider extends AbstractServiceProvider
{
    /**
     *  Handle Facebook response
     *
     *  @return Illuminate\Http\Response
     */
    public function handle()
    {
        $user = $this->createOrGetUser($this->provider->user());

        return $this->login($user);
    }

    /**
     * @param ProviderUser $providerUser
     * @return mixed
     */
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = SocialAccount::whereProvider('google')
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider'         => 'google',
            ]);
            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {
                $user = $this->register([
                    'email'     => $providerUser->getEmail(),
                    'name'      => $providerUser->getName(),
                    'status_id' => 3,
                    'verified'  => true,
                    'avatar'    => $providerUser->getAvatar(),
                ]);

                $user->attachRole(2);
            }
            $account->user()->associate($user);
            $account->save();
            return $user;
        }
    }
}
