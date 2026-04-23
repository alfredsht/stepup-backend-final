<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPegawaiMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $st_pegawai = [
            ['status_pegawai' => 'PNS'],
            ['status_pegawai' => 'Honorer'],
            ['status_pegawai' => 'Pensiun'],
            ['status_pegawai' => 'Kontrak'],
            ['status_pegawai' => 'Guru Tetap'],
        ];

        foreach ($st_pegawai as $status) {
            DB::table('statuspegawai_m')->insert([
                'kdprofile' => '10',
                'statusenabled' => true,
                'status_pegawai' => $status['status_pegawai'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
