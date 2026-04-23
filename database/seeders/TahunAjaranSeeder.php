<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tahun_ajaran_m')->insert([
            [
                'kdprofile' => '10',
                'statusenabled' => true,
                'tahun' => '2023/2024',
                'semester' => 'Ganjil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => '10',
                'statusenabled' => true,
                'tahun' => '2023/2024',
                'semester' => 'Genap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => '10',
                'statusenabled' => true,
                'tahun' => '2024/2025',
                'semester' => 'Ganjil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => '10',
                'statusenabled' => false,
                'tahun' => '2024/2025',
                'semester' => 'Genap',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
