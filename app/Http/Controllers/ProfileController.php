<?php

namespace App\Http\Controllers;

use File;
use Auth;
use Validator;
use App\Entity\User;
use App\Entity\Language;
use Illuminate\Http\Request;

class ProfileController extends Controller
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
        // 
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
    public function show($id)
    {
        $user = Auth::user();
        if(!$user = User::with('language')->find($id)){
            return "error";
        }

        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();

        if($id != $user->id){
            return back()->with(['warning-message' => 'Record not found.']);
        }

        $language = Language::get();

        return view('profile.edit', compact('user', 'language'));
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
        $rules = [
            'name'        => 'required|max:255',
            'contact_no'  => 'nullable|numeric',
            'language'    => 'required',
        ];

        $message = [
            'name.required'         => 'Please enter your name.',
            'contact_no.required'   => 'Please enter your contact no.',
        ];

        $validator = Validator::make($request->input(), $rules, $message);

        if ($validator->fails()) {

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if(!$language = Language::find($request->input('language'))){
            return back()->with(['warning-message' => 'Record not found.']);
        }


        $user = User::find(Auth::user()->id);

        $user->update([
            'name'            => $request->input('name'),
            'contact'         => $request->input('contact_no'),
            'language_id'     => $request->input('language'),
        ]);

        if (!empty($request->file('avatar'))) {
            $avatar = \App\Processors\SaveUserAvatarProcessor::make($request->file('avatar'))->execute();

            if ($user->avatar != null) {
                File::delete(public_path('uploads/avatars'. '/' .$user->avatar));
            }

            $user->forcefill(['avatar' => $avatar])->save();
        }

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
        //
    }
}
