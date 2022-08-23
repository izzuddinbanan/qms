<?php

namespace App\Http\Controllers\Api;

use JWTAuth;

trait JwtAuthTrait
{
    private function getUserFromJwtToken($inputs)
    {
        $JWTAuth = $this->setJwtToken($inputs);
        $User = $this->getUser($JWTAuth);

        return $User;
    }

    private function setJwtToken($inputs)
    {
        if (!$token = isset($inputs['token']) ? $inputs['token'] : '') {
            return $this->returnData([], 'auth', 'error', [Lang::get('middleware.token_error')],$no_app_data = true);
        } else {
            $JWTAuth = JWTAuth::setToken($token);
        }

        return $JWTAuth;
    }

    private function getUser($JWTAuth)
    {
        if (!$User = $JWTAuth->authenticate()) {
            return $this->returnData([], 'auth', 'error', [Lang::get('middleware.user_error')],$no_app_data = true);
        }

        return $User;
    }
}
