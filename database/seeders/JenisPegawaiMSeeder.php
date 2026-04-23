<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPegawaiMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenispegawai_m')->insert([
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'jenispegawai' => 'Kepala Sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'jenispegawai' => 'Guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'jenispegawai' => 'Guru Honorer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'jenispegawai' => 'Staf Administrasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'jenispegawai' => 'Tenaga Kebersihan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'jenispegawai' => 'Security',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
