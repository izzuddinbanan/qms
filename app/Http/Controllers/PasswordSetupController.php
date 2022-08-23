<?php

namespace App\Http\Controllers;

use App\Entity\User;
use App\Entity\PasswordSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordSetupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($token)
    {
        $password_setup = PasswordSetup::all();

        foreach($password_setup as $ps)
        {
            if(Hash::check($token, $ps->token))
            {
                return view('password.setup')->with('token', $token);
            }
        }

        return redirect('login');
        // Hash::check($request->input('data.password'), $user->password)

        // $hashed_token = bcrypt($token);

        // if(PasswordSetup::where('token',$hashed_token)->count()>0)
        // {
        //     dd("token valid");
        // }
        // else{
        //     dd("token invalid");
        // }
        // dd("herere");
        
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        dd("1234");
        return view('password.setupsuccess');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $token)
    {
        $rules = [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6|same:password'
        ];

        $this->validate($request, $rules);

        $password_setup = PasswordSetup::all();

        foreach($password_setup as $ps)
        {
            if(Hash::check($token, $ps->token))
            {
                $email = $ps->email;
                $user = User::where('email', $email)->first();

                $user->forcefill(['password' => bcrypt($request->input('password'))]);
                $user->verified = "1";
                $user->save();

                $password_setup_record = PasswordSetup::find($ps->id);
                $password_setup_record->delete();

                return view('password.setupsuccess');
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
