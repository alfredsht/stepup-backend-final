<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSiswaMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statussiswa_m')->insert([
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'status_siswa' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'status_siswa' => 'Tidak Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}
