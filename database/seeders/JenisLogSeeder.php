<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_log_m')->insert([
            [
                'kdprofile'     => 10,
                'statusenabled' => true,
                'nama_log'      => 'Login',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kdprofile'     => 10,
                'statusenabled' => true,
                'nama_log'      => 'Logout',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kdprofile'     => 10,
                'statusenabled' => true,
                'nama_log'      => 'Create',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kdprofile'     => 10,
                'statusenabled' => true,
                'nama_log'      => 'Update',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kdprofile'     => 10,
                'statusenabled' => true,
                'nama_log'      => 'Delete',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kdprofile'     => 10,
                'statusenabled' => true,
                'nama_log'      => 'Error',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
