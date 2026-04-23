<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgamaMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $religions = [
            ['id' => 1, 'agama' => 'Islam'],
            ['id' => 2, 'agama' => 'Kristen Protestan'],
            ['id' => 3, 'agama' => 'Katolik'],
            ['id' => 4, 'agama' => 'Hindu'],
            ['id' => 5, 'agama' => 'Buddha'],
            ['id' => 6, 'agama' => 'Konghucu'],
        ];

        foreach ($religions as $religion) {
            DB::table('agama_m')->insert([
                'id' => $religion['id'],
                'kdprofile' => 10,
                'statusenabled' => true,
                'agama' => $religion['agama'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
