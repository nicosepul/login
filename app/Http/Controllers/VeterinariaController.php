<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Raza;
use App\Models\Dueno;
use App\Models\Mascota;
use App\Models\Ingreso;

class VeterinariaController
{
    public function obtenerRazas()
    {
        return Raza::all();
    }

    public function obtenerMascotas()
    {
        return Mascota::with(['dueno', 'raza'])->orderBy('id', 'desc')->get();
    }

    public function registroMascotaDueno(Request $request)
    {
        $dueno = Dueno::where('rut', $request->rut)->first();
        
        if (!$dueno) {
            $dueno = new Dueno();
            $dueno->rut = $request->rut;
            $dueno->nombre = $request->nombre_dueno;
            $dueno->apellido = $request->apellido_dueno;
            $dueno->telefono = $request->telefono;
            $dueno->direccion = $request->direccion;
            $dueno->email = $request->email;
            $dueno->save();
        }

        $mascota = new Mascota();
        $mascota->dueno_id = $dueno->id;
        $mascota->raza_id = $request->raza_id;
        $mascota->nombre = $request->nombre_mascota;
        $mascota->especie = $request->especie;
        $mascota->sexo = $request->sexo;
        $mascota->color = $request->color;
        $mascota->fecha_nacimiento = $request->fecha_nacimiento;
        $mascota->peso = $request->peso;
        $mascota->save();

        return response()->json(['mensaje' => 'Mascota y dueño registrados correctamente', 'mascota' => $mascota]);
    }

    public function obtenerMascotasPorRut(Request $request)
    {
        $dueno = Dueno::where('rut', $request->rut)->first();
        if (!$dueno) return response()->json(['mensaje' => 'Dueño no encontrado'], 404);
        return $dueno->mascotas()->with('raza')->get();
    }

    public function registrarIngreso(Request $request)
    {
        $ingreso = new Ingreso();
        $ingreso->mascota_id = $request->mascota_id;
        $ingreso->motivo = $request->motivo;
        $ingreso->diagnostico = $request->diagnostico;
        $ingreso->tratamiento = $request->tratamiento;
        $ingreso->observaciones = $request->observaciones;
        $ingreso->fecha_ingreso = now();
        $ingreso->save();

        return response()->json(['mensaje' => 'Ingreso registrado correctamente', 'ingreso' => $ingreso]);
    }

    public function obtenerIngresos()
    {
        return Ingreso::with(['mascota.dueno', 'mascota.raza'])->orderBy('id', 'desc')->get();
    }

    public function actualizarMascota(Request $request, $id)
    {
        $mascota = Mascota::find($id);
        $mascota->raza_id = $request->raza_id;
        $mascota->nombre = $request->nombre_mascota;
        $mascota->especie = $request->especie;
        $mascota->sexo = $request->sexo;
        $mascota->color = $request->color;
        $mascota->fecha_nacimiento = $request->fecha_nacimiento;
        $mascota->peso = $request->peso;
        $mascota->save();

        $dueno = $mascota->dueno;
        $dueno->nombre = $request->nombre_dueno;
        $dueno->apellido = $request->apellido_dueno;
        $dueno->telefono = $request->telefono;
        $dueno->direccion = $request->direccion;
        $dueno->email = $request->email;
        $dueno->save();

        return response()->json(['mensaje' => 'Mascota y dueño actualizados correctamente']);
    }

    public function eliminarMascota($id)
    {
        Mascota::destroy($id);
        return response()->json(['mensaje' => 'Mascota eliminada correctamente']);
    }
}
