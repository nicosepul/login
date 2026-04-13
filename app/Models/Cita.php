<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'dueno_id',
        'mascota_id',
        'sucursal_id',
        'fecha_cita',
        'hora_cita',
        'estado',
        'prediagnostico',
    ];

    protected $casts = [
        'fecha_cita' => 'date:Y-m-d',
    ];

    public function dueno()
    {
        return $this->belongsTo(Dueno::class);
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}