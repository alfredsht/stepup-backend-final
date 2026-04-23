<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisTransaksiTabunganSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jenis_transaksi_tabungan')->insert([
            ['kode' => 'SETOR', 'nama' => 'Setoran Tabungan', 'kdprofile' => 10],
            ['kode' => 'TARIK', 'nama' => 'Penarikan Tabungan', 'kdprofile' => 10],
            ['kode' => 'CATATAN', 'nama' => 'Catatan Guru', 'kdprofile' => 10],
        ]);
    }
}
