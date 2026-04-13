<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalesSeeder extends Seeder
{
    public function run()
    {
        Sucursal::truncate();

        $sucursales = [
            ['nombre' => 'Sucursal Centro', 'direccion' => 'Av. Principal 123'],
            ['nombre' => 'Sucursal Norte', 'direccion' => 'Ruta 5 Km 10'],
            ['nombre' => 'Sucursal Sur', 'direccion' => 'Calle Secundaria 456'],
        ];

        foreach ($sucursales as $sucursal) {
            Sucursal::updateOrCreate(['nombre' => $sucursal['nombre']], $sucursal);
        }
    }
}