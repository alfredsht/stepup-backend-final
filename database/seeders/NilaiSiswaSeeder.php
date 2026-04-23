<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NilaiSiswaSeeder extends Seeder
{
    public function run(): void
    {
        $siswaList = DB::table('students_m')->inRandomOrder()->limit(50)->pluck('nis');
        $mapelList = DB::table('mapel_m')->pluck('id');
        $kelasList = DB::table('kelas_m')->pluck('id');
        $guruList = DB::table('pegawai_m')->where('objectjenispegawaifk', 1)->pluck('id');
        $tahunAjaranList = DB::table('tahun_ajaran_m')->pluck('id');

        foreach ($siswaList as $nis) {
            
            $mapelId = $mapelList->random();
            $kelasId = $kelasList->random();
            $guruId = $guruList->random();
            $tahunAjaranId = $tahunAjaranList->random();
            $tanggalPenilaian = now()->subDays(rand(0, 365))->format('Y-m-d');

            $nilaiSiswaId = DB::table('nilai_siswa')->insertGetId([
                'siswa_nis' => $nis,
                'mapel_id' => $mapelId,
                'kelas_id' => $kelasId,
                'tahun_ajaran_id' => $tahunAjaranId,
                'guru_id' => $guruId,
                'tanggal_penilaian' => $tanggalPenilaian,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            
            DB::table('detail_nilai_siswa')->insert([
                'nilai_siswa_id' => $nilaiSiswaId,
                'nilai_harian' => rand(70, 100),
                'nilai_pr' => rand(70, 100),
                'nilai_tugas' => rand(70, 100),
                'nilai_uts' => rand(70, 100),
                'nilai_uas' => rand(70, 100),
                'catatan_guru' => 'Simulasi nilai dari seeder.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
