<?php

namespace App\Http\Controllers\Api\v1;

use File;
use JWTAuth;
use Validator;
use App\Entity\User;
use App\Supports\AppData;
use Dingo\Api\Http\Request;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Jrean\UserVerification\Traits\VerifiesUsers;
use App\Http\Controllers\Api\v1\BaseApiController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class AuthController extends BaseApiController
{
    use SendsPasswordResetEmails, VerifiesUsers, AppData;

    /**
     * @var mixed
     */
    protected $error_message;

    public function __construct()
    {
        parent::__construct();

        $this->error_message = [
            'data.name.required'     => "Name is required.",
            'data.email.required'    => "Email is required.",
            'data.email.email'       => "Email is in invalid format.",
            'data.contact.required'  => "Contact is required.",
            'data.contact.numeric'   => "Contact must be in numeric.",
            'data.password.required' => "Password is required.",
            'new_password.regex'     => "The new password must contain at least one uppercase, one lowercase, and one digit.",
        ];
    }

    /**
     * @SWG\Post(
     *     path="/auth/login",
     *     summary="User Login",
     *     method="post",
     *     tags={"Authentication"},
     *     description="This API will authenticate the user email and password. Return whether login is successful.",
     *     operationId="login",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="email",type="string",example="tester@convep.com"),
     *                      @SWG\Property(property="password",type="string",example="password"),
     *                      @SWG\Property(property="push_token",type="string",example="push_token"),
     *                      @SWG\Property(property="os_version",type="string",example="os_version"),
     *                      @SWG\Property(property="app_version",type="string",example="app_version"),
     *               ),
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

        $validator = Validator::make($request->input('data'), $rules, $this->error_message);

        $data = $this->data;

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $credentials = ['email' => $request->input('data.email'), 'password' => $request->input('data.password')];

        if (JWTAuth::attempt($credentials)) {
            $user = User::whereEmail($request->input('data.email'))->first();

            ##only contractor / subcon / owner use mobile
            if($user->hasRole('super_user') || $user->hasRole('power_user') || $user->hasRole('admin')){
                $status = $this->failedAppData('User not exist!');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);
                return new BaseResource($emptyData);
            }

            $token = JWTAuth::fromUser($user, [
                'email'    => $user->email,
                'password' => $user->password,
            ]);

            $user->devices()->firstOrCreate([
                'push_token'  => $request->input('data.push_token'),
                'OS'          => $request->input('data.os_version'),
                'app_version' => $request->input('data.app_version'),
                'width'       => $request->input('data.width'),
                'height'      => $request->input('data.height'),
                'os_version'  => $request->input('data.os_version'),
                'IMEI'        => $request->input('data.IMEI'),
            ]);

            $role_user = $user->role()->first();

            User::where('id', $user->id)->update(['current_role' => $role_user->role_id]);
            $user = User::whereEmail($request->input('data.email'))->first();

            array_push($data, $user);

            $user->appData = $this->prepareAppData($request, $data);
            $user->token = $token;

            return new UserResource($user);
        }

        $status = $this->failedAppData('Invalid credentials!');

        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data, $status);
        return new BaseResource($emptyData);
    }


    /**
     * @SWG\Post(
     *     path="/auth/logout",
     *     summary="Logout",
     *     method="post",
     *     tags={"Authentication"},
     *     description="Use this API to logout.",
     *     operationId="logout",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="push_token",type="string",example="push_token"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $user = $this->user;
        $data = $this->data;
        
        $user->devices()->where('push_token', $request->input('data.push_token'))->delete();
        
        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data);

        return new BaseResource($emptyData);
    }

    /**
     * @SWG\Post(
     *     path="/auth/user",
     *     summary="User Information",
     *     method="post",
     *     tags={"User Profile"},
     *     description="Use this API to retrieve user information.",
     *     operationId="me",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
     *
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function me(Request $request)
    {
        $user = $this->user;

        $data = $this->data;

        array_push($data, $user);

        $user->appData = $this->prepareAppData($request, $data);

        return new UserResource($user);
    }


    /**
     * @SWG\Post(
     *     path="/auth/editUser",
     *     summary="Update User Profile -- upload photo only can run in postman",
     *     method="post",
     *     tags={"User Profile"},
     *     description="Use this API to update user information.",
     *     operationId="edituser",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="name",type="string",example="testing"),
     *                      @SWG\Property(property="contact",type="string",example="0194118978"),
     *                      @SWG\Property(property="language",type="string",example="1"),
     *               ),
     *         ),
     *     ),
     *      @SWG\Parameter(
     *          description="Profile Picture",
     *          in="formData",
     *          name="avatar",
     *          type="file",
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function editUser(Request $request)
    {

        $rules = [
            'name'        => 'required|max:255',
            'language'    => 'required|max:255',
        ];

        $validator = Validator::make($request->input('data'), $rules, $this->error_message);

        $data = $this->data;

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$language = \App\Entity\Language::where('id', $request->input('data.language'))->first()){

            $status = $this->failedAppData(trans('api.notFound'));

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $user = $this->user;

        $user->update([
            'name'          => $request->input('data.name'),
            'contact'       => $request->input('data.contact'),
            'language_id'   => $request->input('data.language'),
        ]);


        if (!empty($request->file('data.avatar'))) {
            $avatar = \App\Processors\SaveUserAvatarProcessor::make($request->file('data.avatar'))->execute();

            if ($user->avatar != null) {
                File::delete($user->avatarStoragePath());
            }

            $user->forcefill(['avatar' => $avatar])->save();
        }

        array_push($data, $user);

        $user->appData = $this->prepareAppData($request, $data);

        return new UserResource($user);

    }

    /**
     * @SWG\Post(
     *     path="/auth/changePassword",
     *     summary="To Change Password of user",
     *     method="post",
     *     tags={"User Profile"},
     *     description="Use this API to change the user password.",
     *     operationId="editpassword",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="password",type="string",example="testing"),
     *                      @SWG\Property(property="new_password",type="string",example="password"),
     *               ),
     *         ),
     *     ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param  Request $request [description]
     * @return [type]           [description]
     */

    public function changePassword(Request $request)
    {

        $rules = [
            'password'     => 'required',
            // 'new_password' => 'required|min:6',
            'new_password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ];

        $validator = Validator::make($request->input('data'), $rules, $this->error_message);

        $data = $this->data;

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $user = $this->user;

        if (!$match = Hash::check($request->input('data.password'), $user->password)) {

            $status = $this->failedAppData('The password is wrong');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $user->forcefill([
            'password' => bcrypt($request->input('data.new_password')),
        ])->save();

        array_push($data, $user);

        $user->appData = $this->prepareAppData($request, $data);

        // return (new \App\Http\Resources\UserCollection($users))->additional(['AppData' => $this->appData]);
        return new UserResource($user);

    }

    /**
     * @SWG\Post(
     *     path="/auth/refresh",
     *     summary="New Time-to-live (TTL) token",
     *     method="post",
     *     tags={"Authentication"},
     *     description="Use this API to get a new access token.",
     *     operationId="refresh",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
     *
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function refresh(Request $request)
    {
        $current_token = JWTAuth::getToken();
        $token = JWTAuth::refresh($current_token);

        // return response()->json(compact('token'));

        $data = $this->data;

        $emptyData = collect(['token' => $token]);
        $emptyData->appData = $this->prepareAppData($request, $data);

        return new BaseResource($emptyData);
    }


    /**
     * @SWG\Post(
     *     path="/auth/pt",
     *     summary="push token",
     *     method="post",
     *     tags={"Authentication"},
     *     description="Use this API to get poush token.",
     *     operationId="pushToken",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="push_token",type="string",example="push_token"),
          *                 @SWG\Property(property="os_version",type="string",example="os_version"),
     *                      @SWG\Property(property="app_version",type="string",example="app_version"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function pushToken(Request $request)
    {

        $user = $this->user;
        $data = $this->data;

        $user->devices()->updateOrCreate([
            'push_token'  => $request->input('data.push_token'),
            'OS'          => $request->input('data.os_version'),
            'app_version' => $request->input('data.app_version'),
            'width'       => $request->input('data.width'),
            'height'      => $request->input('data.height'),
            'os_version'  => $request->input('data.os_version'),
            'IMEI'        => $request->input('data.IMEI'),
        ]);

        array_push($data, $user);

        $user->appData = $this->prepareAppData($request, $data);

        return new UserResource($user);

    }

    /**
     * @SWG\Post(
     *     path="/auth/forgot-password",
     *     summary="User Forgot Password",
     *     method="post",
     *     tags={"Authentication"},
     *     description="Use this API to send reset email to the user.",
     *     operationId="forgotPassword",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="email",type="string",example="example@yahoo.com"),
     *               ),
     *         ),
     *     ),
     *     @SWG\Response(response="204", description="")
     * )
     * @param Request $request
     */
    public function forgotPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email|max:255',
        ];

        $validator = Validator::make($request->input('data'), $rules, $this->error_message);

        $data = $this->data;

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if (User::whereEmail($request->input('data.email'))->doesntExist()) {
            $status = $this->failedAppData('User not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $input = ['email' => $request->input('data.email')];

        $response = $this->broker()->sendResetLink(
            $input
        );

        $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);

        $data = $this->data;

        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data);

        return new BaseResource($emptyData);
    }


}
