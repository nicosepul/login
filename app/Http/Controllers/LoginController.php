<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    public function index()
    {
        if (Auth::guard('user')->check()) {
            return redirect('/');
        }
        return view('login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::guard('user')->attempt($credentials)) {
            return back()->withErrors(['correo' => 'Credenciales incorrectas'])->withInput();
        }

        $request->session()->regenerate();
        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
