<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendidikanMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pendidikan_m')->insert([
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'pendidikan' => 'SD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'pendidikan' => 'SMP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'pendidikan' => 'SMA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'pendidikan' => 'Diploma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'pendidikan' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'pendidikan' => 'S2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'pendidikan' => 'S3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
