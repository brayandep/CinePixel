<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [];

        for ($i = 1; $i <= 7; $i++) {
            $rooms[] = ['name' => "Sala $i", 'created_at' => now(), 'updated_at' => now()];
        }

        Room::insert($rooms);
    }
}
