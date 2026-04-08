<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegistroController
{
    public function index()
    {
        return view('registro_usuario');
    }

    public function paises()
    {
        $respuesta = Http::get('https://www.apicountries.com/countries');

        if ($respuesta->successful()) {
            return response()->json($respuesta->json());
        }

        return response()->json([]);
    }

    public function registrar(Request $solicitud)
    {
        $datosValidados = $solicitud->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'email', 'unique:users,correo'],
            'contrasena' => ['required', 'min:6', 'same:confirmacion_contrasena'],
            'confirmacion_contrasena' => ['required'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'nacionalidad' => ['nullable', 'string'],
        ], [
            'correo.email' => 'El correo debe tener un formato válido.',
            'correo.unique' => 'El correo ya está registrado.',
            'confirmacion_contrasena.required' => 'Debes confirmar la contraseña.',
            'contrasena.same' => 'La confirmación de contraseña no coincide.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser mayor a hoy.',
        ]);

        $usuario = User::create([
            'nombre' => $datosValidados['nombre'],
            'correo' => $datosValidados['correo'],
            'password' => Hash::make($datosValidados['contrasena']),
            'direccion' => $datosValidados['direccion'] ?? null,
            'genero' => $datosValidados['genero'] ?? null,
            'fecha_nacimiento' => $datosValidados['fecha_nacimiento'] ?? null,
            'nacionalidad' => $datosValidados['nacionalidad'] ?? null,
        ]);

        Auth::guard('user')->login($usuario);

        if ($solicitud->expectsJson() || $solicitud->ajax()) {
            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'redirect' => url('/'),
            ]);
        }

        return redirect('/')->with('success', 'Usuario registrado correctamente');
    }

    public function listarUsuarios()
    {
        $usuarios = User::orderByDesc('id')->get()->map(function ($usuario) {
            return [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'direccion' => $usuario->direccion,
                'genero' => $usuario->genero,
                'fecha_nacimiento' => optional($usuario->fecha_nacimiento)->format('Y-m-d'),
                'nacionalidad' => $usuario->nacionalidad,
            ];
        });

        return response()->json($usuarios);
    }

    public function actualizarUsuario(Request $solicitud, User $usuario)
    {
        $datosValidados = $solicitud->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'email', Rule::unique('users', 'correo')->ignore($usuario->id)],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'nacionalidad' => ['nullable', 'string'],
        ], [
            'correo.email' => 'El correo debe tener un formato válido.',
            'correo.unique' => 'El correo ya está registrado.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser mayor a hoy.',
        ]);

        $usuario->update([
            'nombre' => $datosValidados['nombre'],
            'correo' => $datosValidados['correo'],
            'direccion' => $datosValidados['direccion'] ?? null,
            'genero' => $datosValidados['genero'] ?? null,
            'fecha_nacimiento' => $datosValidados['fecha_nacimiento'] ?? null,
            'nacionalidad' => $datosValidados['nacionalidad'] ?? null,
        ]);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'direccion' => $usuario->direccion,
                'genero' => $usuario->genero,
                'fecha_nacimiento' => optional($usuario->fecha_nacimiento)->format('Y-m-d'),
                'nacionalidad' => $usuario->nacionalidad,
            ],
        ]);
    }

    public function eliminarUsuario(User $usuario)
    {
        if (Auth::guard('user')->id() === $usuario->id) {
            return response()->json([
                'message' => 'No puedes eliminar tu propio usuario mientras tienes la sesion activa.',
            ], 422);
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }
}