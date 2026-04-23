<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $absensiData = [];

        // for ($i = 1; $i <= 20; $i++) {
        //     $status = ['Tepat Waktu', 'Terlambat', 'Tidak Hadir'][array_rand(['Tepat Waktu', 'Terlambat', 'Tidak Hadir'])];
        //     $keterangan = null;

        //     // Jika statusnya "Tidak Hadir", isi keterangan dengan "Sakit" atau "Izin"
        //     if ($status === 'Tidak Hadir') {
        //         $keterangan = ['Sakit', 'Izin'][array_rand(['Sakit', 'Izin'])];
        //     }

        //     $absensiData[] = [
        //         'kdprofile' => 10,
        //         'statusenabled' => true,
        //         'objectsiswafk' => 10001 + $i,
        //         'waktu_tap' => Carbon::now()->subDays(rand(1, 30))->setTime(rand(7, 22), rand(0, 59), rand(0, 59)),
        //         'status' => $status,
        //         'keterangan' => $keterangan,
        //     ];
        // }

        // DB::table('absensi_m')->insert($absensiData);
    }
}
