<?php

namespace App\Http\Controllers\Api\v1;

use JWTAuth;
use Validator;
use App\Entity\User;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Api\v1\BaseApiController;

class ThirdPartyAuthController extends BaseApiController
{

    /**
     * @SWG\Post(
     *     path="/third-party/auth/login",
     *     summary="Third Party Login",
     *     method="post",
     *     tags={"Authentication (Third Party)"},
     *     description="This API will authenticate third parties. Return whether login is successful.",
     *     operationId="login",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *                @SWG\Property(property="email",type="string",example="salesforce@commudesk.com"),
     *              @SWG\Property(property="password",type="string",example="password"),
     *         ),
     *      ),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->input(), $rules);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

        config(['jwt.ttl' => 30]);
        if (JWTAuth::attempt($credentials)) {
            $user = User::whereEmail($request->input('email'))->first();

            if (!$user->isThirdParty()) {
                return third_party_response('999999', 'failed', 'Invalid credential.', []);
            }

            $token = JWTAuth::fromUser($user, [
                'email'    => $user->email,
                'password' => $user->password,
            ]);

            $result = [
                'token' => $token,
            ];
            return third_party_response('', 'success', '', $result);
        }

        return third_party_response('999999', 'failed', 'Invalid credential.', []);
    }
}
