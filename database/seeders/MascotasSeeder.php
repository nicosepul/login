<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mascota;
use App\Models\Dueno;
use App\Models\Raza;

class MascotasSeeder extends Seeder
{
    public function run()
    {
        Mascota::truncate();
        $dueno1 = Dueno::first();
        $dueno2 = Dueno::skip(1)->first();
        $raza1 = Raza::where('nombre', 'Pastor Alemán')->first();
        $raza2 = Raza::where('nombre', 'Labrador Retriever')->first();
        $raza3 = Raza::where('nombre', 'Golden Retriever')->first();
        $raza4 = Raza::where('nombre', 'Beagle')->first();

        Mascota::updateOrCreate(
            ['dueno_id' => $dueno1->id, 'nombre' => 'Rex'],
            ['raza_id' => $raza1->id, 'especie' => 'Canino', 'sexo' => 'Macho', 'color' => 'Negro', 'fecha_nacimiento' => '2020-01-01', 'peso' => 25.5]
        );
        Mascota::updateOrCreate(
            ['dueno_id' => $dueno2->id, 'nombre' => 'Luna'],
            ['raza_id' => $raza2->id, 'especie' => 'Canino', 'sexo' => 'Hembra', 'color' => 'Blanco', 'fecha_nacimiento' => '2021-05-15', 'peso' => 18.0]
        );
        Mascota::updateOrCreate(
            ['dueno_id' => $dueno1->id, 'nombre' => 'Toby'],
            ['raza_id' => $raza3->id, 'especie' => 'Canino', 'sexo' => 'Macho', 'color' => 'Dorado', 'fecha_nacimiento' => '2019-11-10', 'peso' => 30.2]
        );
        Mascota::updateOrCreate(
            ['dueno_id' => $dueno2->id, 'nombre' => 'Kira'],
            ['raza_id' => $raza4->id, 'especie' => 'Canino', 'sexo' => 'Hembra', 'color' => 'Café', 'fecha_nacimiento' => '2022-03-20', 'peso' => 12.5]
        );
        Mascota::updateOrCreate(
            ['dueno_id' => $dueno1->id, 'nombre' => 'Max'],
            ['raza_id' => $raza1->id, 'especie' => 'Canino', 'sexo' => 'Macho', 'color' => 'Gris', 'fecha_nacimiento' => '2023-01-05', 'peso' => 22.0]
        );
        Mascota::updateOrCreate(
            ['dueno_id' => $dueno2->id, 'nombre' => 'Nala'],
            ['raza_id' => $raza2->id, 'especie' => 'Canino', 'sexo' => 'Hembra', 'color' => 'Marrón', 'fecha_nacimiento' => '2020-07-12', 'peso' => 19.8]
        );
    }
}
