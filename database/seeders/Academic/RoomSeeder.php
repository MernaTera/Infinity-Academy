<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Academic\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::insert([
            [
                'name' => 'Room 101',
                'branch_id' => 1,
                'capacity' => 20,
                'room_type' => 'Offline',
                'created_by_admin_id' => 1
            ],
            [
                'name' => 'Room 102',
                'branch_id' => 1,
                'capacity' => 15,
                'room_type' => 'Offline',
                'created_by_admin_id' => 1
            ],
            [
                'name' => 'Virtual Room A',
                'branch_id' => 1,
                'capacity' => 999,
                'room_type' => 'Online',
                'created_by_admin_id' => 1
            ],
            [
                'name' => 'Virtual Room B',
                'branch_id' => 1,
                'capacity' => 999,
                'room_type' => 'Online',
                'created_by_admin_id' => 1
            ]
        ]);
    }
}
