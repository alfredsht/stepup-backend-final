<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlamatMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $valuesProvinsi = [
            11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 
            31, 32, 33, 34, 35, 36, 51, 52, 53, 61, 
            62, 63, 64, 65, 71, 72, 73, 74, 75, 76, 
            81, 82, 91, 94
        ];
        $valuesKecamatan = [3201010, 3201020, 3201021, 3201030, 3201040, 3201050, 3201051, 3201060, 3201070, 3201071, 3201080, 3201081, 3201090, 3201100];
        $valuesKota = [3201, 3202, 3203, 3204, 3205, 3206, 3207, 3208, 3209, 3210, 3211, 3212, 3213, 3214];
        $valuesKelurahan = [1101010001, 1101010002, 1101010003, 1101010004, 1101010005, 1101010006, 1101010007, 1101010008, 1101010009, 1101010010, 1101010011, 1101010012, 1101010013, 1101010014];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'id' => $i,
                'kdprofile' => 10,
                'statusenabled' => true,
                'namajalankampung' => 'Jl. Sample ' . $i,
                'rt' => str_pad(rand(1, 10), 2, '0', STR_PAD_LEFT),
                'rw' => str_pad(rand(1, 10), 2, '0', STR_PAD_LEFT),
                'objectnegarafk' => 1,
                'objectprovinsifk' => $valuesProvinsi[array_rand($valuesProvinsi)],
                'objectkotakabupatenfk' => $valuesKota[array_rand($valuesKota)], // Random value from 1 to 10
                'objectkecmatanfk' => $valuesKecamatan[array_rand($valuesKecamatan)],      // Random value from 1 to 10
                'objectdesakelurahanfk' => $valuesKelurahan[array_rand($valuesKelurahan)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('alamat_m')->insert($data);
    }
}
