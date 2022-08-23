<?php

namespace App\Http\Controllers\Manages\Passwords;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdatePasswordController extends Controller
{
    public function index()
    {
        return view('password.index');
    }

    public function store(Request $request)
    {
        $rules = [
            'old_password' => 'required|string',
            // 'password'     => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
        ];

        $this->validate($request, $rules, [
            'password.regex' => 'Password must contain at least one uppercase, one lowercase, and one digit.',
        ]);

        $user = auth()->user();

        if (!$match = \Hash::check($request->input('old_password'), $user->password)) {

            return back()->withErrors('Current password is wrong');
        }

        $user->forcefill(['password' => bcrypt($request->input('password'))])->save();

        return redirect()->back()->with(["success-message" => 'Password changed successfully.']);
    }
}
