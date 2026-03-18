<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Seeder;
use App\Models\Core\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = collect([
            [
                'name' => 'Mansoura Branch',
                'code' => 'MAN',
                'address' => 'El Gomhoria St, Mansoura',
                'phone' => '01000000001',
            ],
            [
                'name' => 'Cairo Branch',
                'code' => 'CAI',
                'address' => 'Nasr City, Cairo',
                'phone' => '01000000002',
            ],
            [
                'name' => 'Alexandria Branch',
                'code' => 'ALX',
                'address' => 'Smouha, Alexandria',
                'phone' => '01000000003',
            ],
        ]);

        $branches->each(function ($branch) {
            Branch::updateOrCreate(
                ['code' => $branch['code']],
                [
                    'name' => $branch['name'],
                    'address' => $branch['address'],
                    'phone' => $branch['phone'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });
    }
}