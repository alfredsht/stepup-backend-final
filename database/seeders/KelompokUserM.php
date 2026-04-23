<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class KelompokUserM extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kelompokuser_m')->insert([
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'kempokuser' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'kempokuser' => 'Operator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => false,
                'kempokuser' => 'Guest',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
