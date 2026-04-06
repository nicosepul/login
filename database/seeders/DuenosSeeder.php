<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dueno;

class DuenosSeeder extends Seeder
{
    public function run()
    {
        Dueno::truncate();
        Dueno::updateOrCreate(['rut' => '11.111.111-1'], ['nombre' => 'Juan', 'apellido' => 'Perez', 'telefono' => '912345678', 'direccion' => 'Calle 123', 'email' => 'juan@perez.cl']);
        Dueno::updateOrCreate(['rut' => '22.222.222-2'], ['nombre' => 'Maria', 'apellido' => 'Gomez', 'telefono' => '987654321', 'direccion' => 'Pasaje 456', 'email' => 'maria@gomez.cl']);
    }
}
