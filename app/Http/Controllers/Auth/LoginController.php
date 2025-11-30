<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginUserRequest;
use Session;

class LoginController extends Controller
{
     public function loginSubmit(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            return redirect()->intended('manager/dashboard')
                ->withSuccess('You have Successfully loggedin');
        }

        return redirect("login")->withSuccess('Oppes! You have entered invalid credentials');
    }

    public function logout() {

        Session::flush();
        Auth::logout();
        return redirect("login")->withSuccess('User Logout Successfully !!');

    }


}