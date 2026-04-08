<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        \App\Models\User::truncate();
        User::updateOrCreate(
            ['correo' => 'test1@example.com'],
            ['nombre' => 'Usuario Prueba 1', 'password' => Hash::make('password123'),]
        );

        User::updateOrCreate(
            ['correo' => 'test2@example.com'],
            ['nombre' => 'Usuario Prueba 2', 'password' => Hash::make('password123'),]
        );
    }
}
