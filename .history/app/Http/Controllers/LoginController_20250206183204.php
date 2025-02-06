<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    function showLoginForm()
    {
        return view('login');
    }

    function login(Request $request)
    {
        if($request->email == 'admin@gmail.com' && $request->password == 'Admin1234#'){
            Session::put('loggedIn', true);
            return redirect()->route('/');
        }else{
            return 
        }
    }
}
