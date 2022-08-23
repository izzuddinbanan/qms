<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use App\Entity\User;
use App\Supports\AppData;
use App\Http\Resources\BaseResource;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Notifications\AccountSuspended as AccountSuspendedNotification;

class VerifyToken extends GetUserFromToken
{
    use AppData;

    /**
     * @var mixed
     */
    private $appData;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return $this->tokenFailureHandler($request, 'token_not_provided');
        }

        if (!$claims = JWTAuth::getPayload()) {
            return $this->tokenFailureHandler($request, 'user_not_found');
        }

        if (!$match = User::where('email', $claims['email'])->where('password', $claims['password'])->exists()) {
            return $this->tokenFailureHandler($request, 'user_not_found');
        }


        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->tokenFailureHandler($request, 'token_expired');
        } catch (JWTException $e) {
            return $this->tokenFailureHandler($request, 'token_invalid');
        }

        if (!$user) {
            return $this->tokenFailureHandler($request, 'user_not_found');
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }

    /**
     * @param $error_message
     */
    private function tokenFailureHandler($request, $error_message = '', $code = 100001)
    {
        $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur')->format('Y-m-d, H:i:s');

        $this->appData = collect([
            'settingIndex' => $now,
            'portal_url'   => route('home'),
            'download_url' => route('home'),
            'web_url'      => route('home'),
            'version'      => '',
        ]);

        $status = $this->failedAppData($error_message, $code);
        $data = array();
        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data, $status);

        return new BaseResource($emptyData);
    }
}
