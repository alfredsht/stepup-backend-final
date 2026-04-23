<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NegaraMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['id' => 1, 'negara' => 'Indonesia'],
            ['id' => 2, 'negara' => 'Malaysia'],
            ['id' => 3, 'negara' => 'Singapura'],
            ['id' => 4, 'negara' => 'Thailand'],
            ['id' => 5, 'negara' => 'Vietnam'],
            ['id' => 6, 'negara' => 'Filipina'],
            ['id' => 7, 'negara' => 'Brunei'],
            ['id' => 8, 'negara' => 'Kamboja'],
            ['id' => 9, 'negara' => 'Laos'],
            ['id' => 10, 'negara' => 'Myanmar'],
        ];

        foreach ($countries as $country) {
            DB::table('negara_m')->insert([
                'id' => $country['id'],
                'kdprofile' => 10,
                'statusenabled' => true,
                'negara' => $country['negara'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
