<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    use HasFactory;

    protected $fillable = ['dueno_id', 'raza_id', 'nombre', 'especie', 'sexo', 'color', 'fecha_nacimiento', 'peso'];

    public function dueno()
    {
        return $this->belongsTo(Dueno::class);
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class);
    }

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class);
    }
}
