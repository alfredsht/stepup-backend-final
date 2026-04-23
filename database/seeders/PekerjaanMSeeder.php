<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PekerjaanMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pekerjaanList = [
            'Tidak Bekerja',
            'Mengurus Rumah Tangga',
            'Pelajar/ Mahasiswa',
            'Pegawai Swasta',
            'Pegawai Negeri/ Bumn/ Bumd',
            'TNI/ Polisi',
            'Wiraswasta/ Pengusaha',
            'Pejabat Negara',
            'Lain-Lain',
            'Petani',
            'Wiraswasta',
            'Pensiunan',
            'Buruh',
            'PNS',
            'Swasta',
            'PNS ( Polri)',
            'Ibu R.Tangga',
            'Pelajar',
            'Peg. Kontrak',
            'Blm Bekerja',
            'Purnawirawan',
            'TNI',
            'Mahasiswa',
            'Pedagang',
            'Polri',
            'Peg. Honorer',
            'Siswa Taruna',
            'Calon Karyawan',
            'Nelayan',
            'Profesional/Profesi',
            'Guru',
            'Tenaga Medis',
            'Pengajar',
            'Dokter',
            'BUMN',
            'Dosen',
            'Profesional',
            'BUMD',
            'Dibawah Umur',
            'Pengacara',
            'Konsultan',
            'Pendeta',
            'Pembantu Rumah Tangga',
            'Notaris'
        ];

        foreach ($pekerjaanList as $pekerjaan) {
            DB::table('pekerjaan_m')->insert([
                'kdprofile' => 10, // atau sesuaikan dengan kebutuhan
                'statusenabled' => true,
                'pekerjaan' => $pekerjaan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
