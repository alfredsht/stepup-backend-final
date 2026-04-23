<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'MAT01',
            'nama_mapel' => 'Matematika',
            'nama_singkat' => 'Mat',
            'deskripsi' => 'Mata pelajaran Matematika untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'BAH01',
            'nama_mapel' => 'Bahasa Indonesia',
            'nama_singkat' => 'B. Indo',
            'deskripsi' => 'Mata pelajaran Bahasa Indonesia untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'IPA01',
            'nama_mapel' => 'Ilmu Pengetahuan Alam',
            'nama_singkat' => 'IPA',
            'deskripsi' => 'Mata pelajaran IPA untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'IPS01',
            'nama_mapel' => 'Ilmu Pengetahuan Sosial',
            'nama_singkat' => 'IPS',
            'deskripsi' => 'Mata pelajaran IPS untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'PKN01',
            'nama_mapel' => 'Pendidikan Kewarganegaraan',
            'nama_singkat' => 'PPKn',
            'deskripsi' => 'Mata pelajaran PKN untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'SBK01',
            'nama_mapel' => 'Seni Budaya dan Keterampilan',
            'nama_singkat' => 'SBK',
            'deskripsi' => 'Mata pelajaran SBK untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'PJOK01',
            'nama_mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
            'nama_singkat' => 'PJOK',
            'deskripsi' => 'Mata pelajaran PJOK untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'AGM01',
            'nama_mapel' => 'Pendidikan Agama',
            'nama_singkat' => 'Agama',
            'deskripsi' => 'Mata pelajaran Pendidikan Agama untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'ENG01',
            'nama_mapel' => 'Bahasa Inggris',
            'nama_singkat' => 'B. Inggris',
            'deskripsi' => 'Mata pelajaran Bahasa Inggris untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'statusenabled' => true,
            'kdprofile' => '10',
            'kode_mapel' => 'TEM01',
            'nama_mapel' => 'Tematik',
            'nama_singkat' => 'Tematik',
            'deskripsi' => 'Mata pelajaran Tematik untuk kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ],

    ];
        DB::table('mapel_m')->where('kdprofile', '10')->delete();
        DB::table('mapel_m')->insert($data);
    }
}
