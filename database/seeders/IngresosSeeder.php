<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingreso;
use App\Models\Mascota;

class IngresosSeeder extends Seeder
{
    public function run()
    {
        Ingreso::truncate();
        $mascota1 = Mascota::first();
        $mascota2 = Mascota::skip(1)->first();

        Ingreso::updateOrCreate(
            ['mascota_id' => $mascota1->id, 'motivo' => 'Control mensual'],
            ['diagnostico' => 'Sano', 'tratamiento' => 'Ninguno', 'observaciones' => 'Buen estado de salud', 'fecha_ingreso' => now()]
        );
        Ingreso::updateOrCreate(
            ['mascota_id' => $mascota2->id, 'motivo' => 'Vacunación'],
            ['diagnostico' => 'Sano', 'tratamiento' => 'Vacuna antirrábica', 'observaciones' => 'Próxima cita en un año', 'fecha_ingreso' => now()]
        );
    }
}
