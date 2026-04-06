<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RazasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Raza::truncate();
        $razas = [
            'Mestizo', 'Pastor Alemán', 'Golden Retriever', 'Labrador Retriever',
            'Bulldog Francés', 'Beagle', 'Poodle', 'Chihuahua', 'Dachshund',
            'Boxer', 'Rottweiler', 'Siberian Husky', 'Pug', 'Shih Tzu'
        ];

        foreach ($razas as $nombre) {
            $raza = new \App\Models\Raza();
            $raza->nombre = $nombre;
            $raza->save();
        }
    }
}
