<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'brayandep'],
            [
                'name' => 'brayandep',
                'email' => 'brayandeo@cinepixel.com',
                'role' => 'admin',
                'status' => 'activo',
                'password' => Hash::make('76986478a'),
            ]
        );
    }
}
