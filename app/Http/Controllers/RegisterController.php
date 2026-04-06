<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController 
{
    public function index()
    {
        return view('register');
    }

    public function countries()
    {
        $response = Http::get('https://www.apicountries.com/countries');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'nacionalidad' => ['nullable', 'string'], // aquí guardas numericCode
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'direccion' => $request->direccion,
            'genero' => $request->genero,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'nacionalidad' => $request->nacionalidad, // numericCode
        ]);

        Auth::guard('user')->login($user);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'redirect' => url('/'),
            ]);
        }

        return redirect('/')->with('success', 'Usuario registrado correctamente');
    }
}