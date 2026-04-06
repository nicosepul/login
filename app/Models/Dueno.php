<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dueno extends Model
{
    use HasFactory;

    protected $fillable = ['rut', 'nombre', 'apellido', 'telefono', 'direccion', 'email'];

    public function mascotas()
    {
        return $this->hasMany(Mascota::class);
    }
}
