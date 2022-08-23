<?php

namespace App\Http\Controllers;

use File, Validator;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\Project;
use App\Entity\RoleUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('auth');

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  

        $param = request()->query();
        $search = app('request')->input('search');

        if(empty($param) || (!isset($param['sort']))){
            $sort = "created_at";
            $order = "desc";
        }else{
            $sort = $param['sort'];
            $order = $param['order'];
        }

        $client_id = RoleUser::find(session('role_user_id'));

        $data = RoleUser::where('client_id', $client_id->client_id)
                        ->search($search, null, true, true)
                        ->whereIn('role_id', [3,4, 8])
                        ->where('project_id', 0)
                        ->with('users')
                        ->with('roles')
                        ->orderBy($sort, $order)
                        ->paginate(20);

        $role = Role::whereIn('id', [3, 4, 8])->get();
        return view('user.index', compact('data', 'role'), [$data->appends(Input::except(array('page')))]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
          'name'            => 'required',
          'password'        => 'required',
          'email'           => 'required|email',
          'role'            => 'required',
          'contact'         => 'numeric|nullable',
        ];

        $message = [
            'contact.numeric'       => "Contact must be in numeric.",
        ];

        $validator = Validator::make($request->input(), $rules, $message);  

        if ($validator->fails()) {
          return back()
              ->withErrors($validator)
              ->with(["modal" => "show"])
              ->withInput();
        }

        if($userVerified = User::withTrashed()->where('email', $request->input('email'))->first()){
            return back()
              ->withErrors("Email already exist.")
              ->with(["modal" => "show"])
              ->withInput();
        }

        $client_id = RoleUser::find(session('role_user_id'));

        $user = User::create([
            'name'              => $request->input('name'),
            'email'             => $request->input('email'),
            'password'          => bcrypt($request->input('password')),
            'contact'           => $request->input('contact'),
        ]);

        $role_user = RoleUser::create([
            'user_id'           => $user->id,
            'role_id'           => $request->input('role'),
            'client_id'         => $client_id->client_id
        ]);

        return back()->with(["success-message" => "New record successfully added."]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        // return view()
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
       
       $role_id = $request->input('role_id');

       $user = RoleUser::where('id', $role_id)->with('users')->first();

       return $user;
    }

    /**
     * @param User $profile
     */
    public function editpassword(User $profile)
    {

        return view('password');
    }

    /**
     * @param User $profile
     */
    public function updatePassword(Request $request)
    {
        $rules = [
            'old_password' => 'required|string',
            'password'     => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
        ];

        $this->validate($request, $rules, [
            'password.regex' => 'Password must contain at least one uppercase, one lowercase, and one digit.',
        ]);

        $user = auth()->user();

        if (!$match = \Hash::check($request->input('old_password'), $user->password)) {

            return back()->withErrors('Current password is wrong');
        }

        $user->forcefill(['password' => bcrypt($request->input('password'))])->save();

        return redirect()->route('profile.edit')->withSuccess('Password changed successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $profile)
    {
        $user = auth()->user();
        $user->update([
            'name'    => $request->input('name'),
            'email'   => $request->input('email'),
            'contact' => $request->input('contact'),
        ]);

        if (!empty($request->file('avatar'))) {
            $avatar = \App\Processors\SaveUserAvatarProcessor::make($request->file('avatar'))->execute();

            if ($user->avatar != null) {
                File::delete($user->avatarStoragePath());
            }

            $user->forcefill(['avatar' => $avatar])->save();
        }

        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $project_id = session('project_id');

        $role_user = RoleUser::find($id);

        $client_id = RoleUser::find(session('role_user_id'));

        if($role_user->user_id == Project::where('id', $project_id)->first()->default_project_team_id)
        {
            return back()->with(['warning-message' => 'Please remove this user from Main Project Team before delete this user.']);
        }
        else{
            RoleUser::where('user_id', $role_user->user_id)->whereIn('role_id', [3, 4, 8])->where('client_id', $client_id->client_id)->delete();

            if(!$checkUser = RoleUser::where('user_id', $role_user->user_id)->first()){

                $user = User::find($role_user->user_id);

                $user->update([
                    'email' => $user->email . "_delete_" . NOW(),
                ]);

                User::where('id', $user->id)->delete();
            }    
            
            return back()->with(['success-message' => 'Record successfully deleted.']);

        }

    }

    public function listRole(){

        $data = Role::whereNotIn('id', [1, 2, 5, 6, 7])->get();

        return $data;

    }

    function updateUser(Request $request){


        $user = User::find($request->input('id'));

        $user->update([
            'name'      => $request->input("name"),
            'contact'   => $request->input("contact"),

        ]);

        return back()->with(['success-message' => 'Record successfully updated.']);
    }
}
