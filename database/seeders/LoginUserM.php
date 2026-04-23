<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginUserM extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('loginuser_m')->insert([
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'kodelogin' => 'admin',
                'katasandi' => Hash::make('password123'),
                'namauser' => 'admin',
                'kelompokuserfk' => 1, // Assume kelompokuser_m.id = 1 exists
                'objectpegawaifk' => 3, // Assume pegawai_m.id = 3 exists
                'no_hp' => '081234567890', // Tambahan no_hp
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => true,
                'kodelogin' => 'operator',
                'katasandi' => Hash::make('operator123'),
                'namauser' => 'operator',
                'kelompokuserfk' => 2, // Assume kelompokuser_m.id = 2 exists
                'objectpegawaifk' => 2, // Assume pegawai_m.id = 2 exists
                'no_hp' => '081298765432',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => false,
                'kodelogin' => 'guest',
                'katasandi' => Hash::make('guest123'),
                'namauser' => 'guest',
                'kelompokuserfk' => 3, // Assume kelompokuser_m.id = 3 exists
                'objectpegawaifk' => 1, // Assume pegawai_m.id = 3 exists
                'no_hp' => '081376543210',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => false,
                'kodelogin' => 'guru4',
                'katasandi' => Hash::make('guru1234'),
                'namauser' => 'guest',
                'kelompokuserfk' => 3, // Assume kelompokuser_m.id = 3 exists
                'objectpegawaifk' => 4, // Assume pegawai_m.id = 4 exists
                'no_hp' => '081323456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => false,
                'kodelogin' => 'guru5',
                'katasandi' => Hash::make('guru1234'),
                'namauser' => 'guest',
                'kelompokuserfk' => 3, // Assume kelompokuser_m.id = 3 exists
                'objectpegawaifk' => 5, // Assume pegawai_m.id = 5 exists
                'no_hp' => '081345678901',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kdprofile' => 10,
                'statusenabled' => false,
                'kodelogin' => 'guru6',
                'katasandi' => Hash::make('guest1234'),
                'namauser' => 'guest',
                'kelompokuserfk' => 3, // Assume kelompokuser_m.id = 3 exists
                'objectpegawaifk' => 5, // Assume pegawai_m.id = 6 exists
                'no_hp' => '081312345678',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
    }
}
