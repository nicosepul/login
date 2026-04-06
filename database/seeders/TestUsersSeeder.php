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
            ['email' => 'test1@example.com'],
            ['name' => 'Usuario Prueba 1', 'password' => Hash::make('password123'), 'role_id' => 4, 'active' => true, 'removed' => false]
        );

        User::updateOrCreate(
            ['email' => 'test2@example.com'],
            ['name' => 'Usuario Prueba 2', 'password' => Hash::make('password123'), 'role_id' => 4, 'active' => true, 'removed' => false]
        );
    }
}
