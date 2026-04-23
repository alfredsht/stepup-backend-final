<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        // Ambil semua provinsi
        $provinces = DB::table('provinsi_m')->pluck('id')->toArray();

        for ($i = 1; $i <= 200; $i++) {
            // Pilih satu provinsi secara acak
            $provinsiId = $provinces[array_rand($provinces)];

            // Ambil kabupaten/kota berdasarkan provinsi yang dipilih
            $kabupatenIds = DB::table('kabupatenkota_m')
                ->where('objectprovinsifk', $provinsiId)
                ->pluck('id')
                ->toArray();
            $kabkotId = $kabupatenIds ? $kabupatenIds[array_rand($kabupatenIds)] : null;

            // Ambil kecamatan berdasarkan kabupaten/kota yang dipilih
            $kecamatanIds = DB::table('kecamatan_m')
                ->where('objectkabupatenkotafk', $kabkotId)
                ->pluck('id')
                ->toArray();
            $kecamatanId = $kecamatanIds ? $kecamatanIds[array_rand($kecamatanIds)] : null;

            // Ambil kelurahan berdasarkan kecamatan yang dipilih
            $kelurahanIds = DB::table('desakelurahan_m')
                ->where('objectkecamatanfk', $kecamatanId)
                ->pluck('id')
                ->toArray();
            $kelurahanId = $kelurahanIds ? $kelurahanIds[array_rand($kelurahanIds)] : null;

            $data[] = [
                'nis' => 10001 + $i,
                'nisn' => 241010 + $i,
                'kdprofile' => '10',
                'statusenabled' => true,
                'namalengkap' => 'Siswa ' . $i,
                'tanggal_lahir' => now()->subYears(rand(10, 18))->subDays(rand(0, 365))->toDateString(),
                'tempat_lahir' => 'Kota ' . chr(64 + $i % 26),
                'alamat' => 'Alamat Siswa ' . $i,
                'no_handphone' => '0812' . rand(10000000, 99999999),
                'provinsifk' => $provinsiId,
                'kabkotafk' => $kabkotId,
                'kecamatanfk' => $kecamatanId,
                'kelurahanfk' => $kelurahanId,
                'nama_ayah' => 'Ayah ' . $i,
                'nama_ibu' => 'Ibu ' . $i,
                'pekerjaanayahfk' => rand(1, 44),
                'pekerjaanibufk' => rand(1, 44),
                'jeniskelaminfk' => rand(1, 2),
                'agamafk' => rand(1, 6),
                'kelasfk' => rand(1, 6),
                'tahun_masuk' => now()->subYears(rand(1, 5))->year,
                'statussiswafk' => rand(1, 2),
                'nfc_tag_id' => 'NFC-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('students_m')->insert($data);
    }
}
