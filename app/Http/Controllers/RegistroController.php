<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistroController
{
    private function datosParaPersistirUsuario(array $datosValidados, bool $incluyeContrasena = false): array
    {
        $datosUsuario = [
            'nombre' => $datosValidados['nombre'],
            'correo' => $datosValidados['correo'],
            'direccion' => $datosValidados['direccion'] ?? null,
            'genero' => $datosValidados['genero'] ?? null,
            'fecha_nacimiento' => $datosValidados['fecha_nacimiento'] ?? null,
            'nacionalidad' => $datosValidados['nacionalidad'] ?? null,
        ];

        if ($incluyeContrasena && isset($datosValidados['contrasena']) && $datosValidados['contrasena']) {
            $datosUsuario['password'] = Hash::make($datosValidados['contrasena']);
        }

        return $datosUsuario;
    }

    private function transformarUsuario(User $usuario): array
    {
        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'direccion' => $usuario->direccion,
            'genero' => $usuario->genero,
            'fecha_nacimiento' => optional($usuario->fecha_nacimiento)->format('Y-m-d'),
            'nacionalidad' => $usuario->nacionalidad,
        ];
    }

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

    public function registrar(StoreUsuarioRequest $solicitud)
    {
        $datosValidados = $solicitud->validated();

        $usuario = User::create($this->datosParaPersistirUsuario($datosValidados, true));

        Auth::guard('user')->login($usuario);

        if ($solicitud->expectsJson() || $solicitud->ajax()) {
            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'redirect' => url('/'),
            ]);
        }

        return redirect('/')->with('success', 'Usuario registrado correctamente');
    }

    public function crearUsuario(StoreUsuarioRequest $solicitud)
    {
        $datosValidados = $solicitud->validated();

        $usuario = User::create($this->datosParaPersistirUsuario($datosValidados, true));

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'usuario' => $this->transformarUsuario($usuario),
        ]);
    }

    public function listarUsuarios()
    {
        $usuarios = User::orderByDesc('id')->get()->map(fn(User $usuario) => $this->transformarUsuario($usuario));

        return response()->json($usuarios);
    }

    public function actualizarUsuario(UpdateUsuarioRequest $solicitud, User $usuario)
    {
        $datosValidados = $solicitud->validated();

        $usuario->update($this->datosParaPersistirUsuario($datosValidados, true));

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $this->transformarUsuario($usuario),
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