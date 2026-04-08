<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'direccion',
        'genero',
        'fecha_nacimiento',
        'nacionalidad',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // Edad calculada dinámicamente
    protected $appends = ['edad'];

    public function getEdadAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }

        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }
}