<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        $this->call(TestUsersSeeder::class);
        $this->call(RazasSeeder::class);
        $this->call(SucursalesSeeder::class);
        $this->call(DuenosSeeder::class);
        $this->call(MascotasSeeder::class);
        $this->call(IngresosSeeder::class);
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
