<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $usuarioId = $usuario instanceof User ? $usuario->id : $usuario;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'email', 'regex:/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/', Rule::unique('users', 'correo')->ignore($usuarioId)],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'nacionalidad' => ['nullable', 'string'],
            'contrasena' => ['nullable', 'string', 'min:8', 'confirmed'],
            'contrasena_confirmation' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.email' => 'El correo debe tener un formato valido.',
            'correo.regex' => 'El correo debe terminar con un dominio, por ejemplo: usuario@correo.com.',
            'correo.unique' => 'El correo ya esta registrado.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser mayor a hoy.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
