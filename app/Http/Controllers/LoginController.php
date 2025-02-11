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
        if($request->email == 'oparation@ginteja.com' && $request->password == 'Ginteja@2025'){
            Session::put('loggedIn', true);
            return redirect()->route('index');
        }else{
            return back()->withErrors(['Invalid Credentials']);
        }
    }

    function logout()
    {
        Session::forget('loggedIn');
        return redirect()->route('login');
    }
}
