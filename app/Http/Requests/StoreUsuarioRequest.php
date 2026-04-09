<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'email', 'regex:/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/', 'unique:users,correo'],
            'contrasena' => ['required', 'min:6', 'same:confirmacion_contrasena'],
            'confirmacion_contrasena' => ['required'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'nacionalidad' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.email' => 'El correo debe tener un formato valido.',
            'correo.regex' => 'El correo debe terminar con un dominio, por ejemplo: usuario@correo.com.',
            'correo.unique' => 'El correo ya esta registrado.',
            'confirmacion_contrasena.required' => 'Debes confirmar la contrasena.',
            'contrasena.same' => 'La confirmacion de contrasena no coincide.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser mayor a hoy.',
        ];
    }
}
