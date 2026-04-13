<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Dueno;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CitaController
{
    private const ESTADO_PENDIENTE = 'pendiente';
    private const ESTADO_ATENDIDO = 'atendido';

    public function obtenerDuenos()
    {
        return Dueno::orderBy('nombre')->get();
    }

    public function obtenerMascotasDelDueno(Dueno $dueno)
    {
        return $dueno->mascotas()->with('raza')->orderBy('nombre')->get();
    }

    public function obtenerSucursales()
    {
        return DB::table('sucursales')->orderBy('nombre')->get();
    }

    public function verificarDisponibilidad(Request $request)
    {
        $datos = $request->validate([
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'fecha_cita' => ['required', 'date'],
            'hora_cita' => ['required', 'date_format:H:i'],
        ]);

        $minutos = (int) substr($datos['hora_cita'], 3, 2);

        if ($minutos % 10 !== 0) {
            return response()->json([
                'message' => 'La hora de la cita debe seleccionarse en intervalos de 10 minutos.',
            ], 422);
        }

        $ocupada = Cita::where('sucursal_id', $datos['sucursal_id'])
            ->whereDate('fecha_cita', $datos['fecha_cita'])
            ->where('hora_cita', $datos['hora_cita'])
            ->exists();

        return response()->json([
            'disponible' => !$ocupada,
            'message' => $ocupada
                ? 'La hora seleccionada ya está tomada en esa sucursal.'
                : 'La hora seleccionada está disponible.',
        ]);
    }

    public function registrar(Request $request)
    {
        $datos = $request->validate([
            'dueno_id' => ['required', 'integer', 'exists:duenos,id'],
            'mascota_id' => ['required', 'integer', 'exists:mascotas,id'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'fecha_cita' => ['required', 'date'],
            'hora_cita' => ['required', 'date_format:H:i'],
            'prediagnostico' => ['nullable', 'string', 'max:2000'],
        ]);

        $minutos = (int) substr($datos['hora_cita'], 3, 2);

        if ($minutos % 10 !== 0) {
            return response()->json([
                'message' => 'La hora de la cita debe seleccionarse en intervalos de 10 minutos.',
            ], 422);
        }

        $datos['estado'] = self::ESTADO_PENDIENTE;

        $mascota = Mascota::where('id', $datos['mascota_id'])
            ->where('dueno_id', $datos['dueno_id'])
            ->first();

        if (!$mascota) {
            return response()->json([
                'message' => 'La mascota seleccionada no pertenece al dueño elegido.',
            ], 422);
        }

        $existeCita = Cita::where('sucursal_id', $datos['sucursal_id'])
            ->whereDate('fecha_cita', $datos['fecha_cita'])
            ->where('hora_cita', $datos['hora_cita'])
            ->exists();

        if ($existeCita) {
            return response()->json([
                'message' => 'Ya existe una cita en esa fecha, hora y sucursal.',
            ], 422);
        }

        $cita = Cita::create($datos);

        return response()->json([
            'message' => 'Cita veterinaria registrada correctamente.',
            'cita' => $cita->load(['dueno', 'mascota.raza', 'sucursal']),
        ]);
    }

    public function obtenerCitas()
    {
        $citas = Cita::with(['dueno', 'mascota', 'sucursal'])
            ->orderBy('fecha_cita', 'asc')
            ->orderBy('hora_cita', 'asc')
            ->get();

        return $citas->map(function (Cita $cita) {
            $estadoVisual = $this->obtenerEstadoVisual($cita);
            $fechaNormalizada = $cita->fecha_cita instanceof Carbon
                ? $cita->fecha_cita->format('Y-m-d')
                : substr((string) $cita->fecha_cita, 0, 10);
            $horaNormalizada = substr((string) $cita->hora_cita, 0, 5);

            return [
                'id' => $cita->id,
                'dueno' => $cita->dueno ? trim(($cita->dueno->nombre ?? '') . ' ' . ($cita->dueno->apellido ?? '')) : '-',
                'mascota' => $cita->mascota->nombre ?? '-',
                'sucursal' => $cita->sucursal->nombre ?? '-',
                'fecha_cita' => $fechaNormalizada,
                'hora_cita' => $horaNormalizada,
                'estado' => $estadoVisual,
                'estado_original' => $cita->estado,
            ];
        });
    }

    public function marcarAtendido(Cita $cita)
    {
        if ($cita->estado === self::ESTADO_ATENDIDO) {
            return response()->json([
                'message' => 'La cita ya se encuentra marcada como atendida.',
            ], 422);
        }

        if ($this->obtenerEstadoVisual($cita) === 'No asistió') {
            return response()->json([
                'message' => 'No se puede marcar como atendida una cita cuyo horario ya pasó y figura como no asistió.',
            ], 422);
        }

        $cita->estado = self::ESTADO_ATENDIDO;
        $cita->save();

        return response()->json([
            'message' => 'La cita fue marcada como atendida.',
        ]);
    }

    private function obtenerEstadoVisual(Cita $cita)
    {
        if ($cita->estado === self::ESTADO_ATENDIDO) {
            return 'Atendido';
        }

        $fecha = $cita->fecha_cita instanceof Carbon
            ? $cita->fecha_cita->format('Y-m-d')
            : substr((string) $cita->fecha_cita, 0, 10);
        $horaCompleta = substr((string) $cita->hora_cita, 0, 8);

        if (strlen($horaCompleta) === 5) {
            $horaCompleta .= ':00';
        }

        $fechaHoraCita = Carbon::createFromFormat('Y-m-d H:i:s', $fecha . ' ' . $horaCompleta);

        if ($fechaHoraCita->lt(Carbon::now())) {
            return 'No asistió';
        }

        return 'Pendiente';
    }
}