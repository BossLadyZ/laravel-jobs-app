<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    //Register Form

    public function register()
    {
        return view('users.register');
    }

    public function login()
    {
        return view('users.login');
    }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($data)) {
            $request->session()->regenerate();

            return redirect('/')->with('message', 'You have been logged in');
        }

        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');

    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|min:3',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        //Hash Password
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        //Login
        auth()->login($user);

        return redirect('/')->with('message', 'user created and logged in');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out!');
    }
}
