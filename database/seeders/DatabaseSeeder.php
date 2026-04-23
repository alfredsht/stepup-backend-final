<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AgamaMSeeder::class);
        $this->call(JenisKelaminMSeeder::class);
        $this->call(NegaraMSeeder::class);
        $this->call(ProvinsiMSeeder::class);
        $this->call(KabupatenKotaMSeeder::class);
        $this->call(KecamatanMSeeder::class);
        $this->call(DesaKelurahanMSeeder::class);
        $this->call(KelasMSeeder::class);
        $this->call(PekerjaanMSeeder::class);
        $this->call(StatusSiswaMSeeder::class);
        $this->call(AlamatMSeeder::class);
        $this->call(StudentsMSeeder::class);
        $this->call(KelompokUserM::class);
        $this->call(AbsensiMSeeder::class);
        $this->call(PendidikanMSeeder::class);
        $this->call(JenisPegawaiMSeeder::class);
        $this->call(PegawaiMSeeder::class);
        $this->call(LoginUserM::class);
        $this->call(OtpRequestsSeeder::class);
        $this->call(MapelMSeeder::class);
        $this->call(TahunAjaranSeeder::class);
        $this->call(NilaiSiswaSeeder::class);
        $this->call(StatusPegawaiMSeeder::class);
        $this->call(JenisTransaksiTabunganSeeder::class);
        $this->call(JenisLogSeeder::class);
        $this->call(MappingMenuSeeder::class);
    }
}
