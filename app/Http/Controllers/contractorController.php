<?php

namespace App\Http\Controllers;

use Validator;
use App\Entity\User;
use App\Entity\RoleUser;
use App\Entity\GroupContractor;
use App\Entity\GroupUser;
use Illuminate\Http\Request;

class contractorController extends Controller
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
    public function index($id)
    {

        return view('contractor_users.index');
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
          'email'           => 'required',
          'name'            => 'required',
          'contact_no'      => 'numeric',
          'password'        => 'required',
        ];

        $message = [
            'contact_no.numeric'     => 'Contact No is required',
        ];

        $validator = Validator::make($request->input(), $rules, $message);

        ##for user/email that not exist
        if(!$user = User::where('email', $request->input('email'))->first()){

            $user = User::create([
                'email'         => $request->input('email'),
                'name'          => $request->input('name'),
                'contact'       => $request->input('contact_no'),
                'password'      => bcrypt($request->input('password')),
            ]);
        }

        $role_user = RoleUser::find(session('role_user_id'));

        RoleUser::create([
            'user_id'       => $user->id,
            'role_id'       => 5,
            'group_id'      => $request->input('group_id'),
            'client_id'     => $role_user->client_id,
        ]);
        return back()->with(['success-message'  => 'New record successfully added.']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $role_user = RoleUser::find(session('role_user_id'));

        if(!$GroupContractor = GroupContractor::where('id', $id)->where('client_id', $role_user->client_id)->first()){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $data = RoleUser::where('role_id', 5)
                        ->with('users')
                        ->where('group_id', $id)
                        // ->groupBy('user_id')
                        ->paginate(20);

        return view('contractor_users.index', compact('GroupContractor', 'data'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $user_id = $request->id;

        $role_user = RoleUser::find(session('role_user_id'));

        if(!RoleUser::where('user_id', $user_id)->where('client_id', $role_user->client_id)->first()){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }
            
        if(!$user = User::find($user_id)){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        return $user;

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $user_id = $request->input('user_id');

        $role_user = RoleUser::find(session('role_user_id'));

        if(!RoleUser::where('user_id', $user_id)->where('client_id', $role_user->client_id)->first()){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }
            
        if(!$user = User::find($user_id)){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        $user->update([
            'name'          => $request->input('name'),
            'contact'       => $request->input('contact_no'),
        ]);

        return back()->with(['success-message' => 'Record successfully updated.']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $role_user = RoleUser::find(session('role_user_id'));

        if(!$user = User::find($id)){
            return back()
              ->withErrors('Record not found.')
              ->withInput();
        }

        if(!$checkContractor = RoleUser::where('user_id', $user->id)->where('role_id', 5)->first()){

            return back()
              ->withErrors('Contractor not found.')
              ->withInput();
        }

        RoleUser::where('user_id', $user->id)->where('role_id', 5)->where('client_id', $role_user->client_id)->delete();


        if(!$checkUser = RoleUser::where('user_id', $user->id)->first()){

            if ($user->avatar != null) {
                File::delete(public_path('uploads/avatars/') .  $user->avatar);
            }

            $user->update([
                'email' => $user->email . "_delete_" . NOW(),
            ]);
            $user->delete();
        }

        return back()->with(['success-message' => "Record successfully deleted."]);
        
    }


    public function verifyUser(Request $request){
        
        $role_user = RoleUser::find(session('role_user_id'));

        if($user = User::where('email', $request->input('email'))->first()){

            if($role_user = RoleUser::where('user_id', $user->id)
                                ->where('role_id', 5)
                                ->where('client_id', $role_user->client_id )
                                ->first())
            {
                return array('errors'   =>  'Email already exist.');
            }

            return $user;
        }

    }

    public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
