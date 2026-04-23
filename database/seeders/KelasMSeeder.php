<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar angka romawi
        $romawi = ['Kelas I', 'Kelas II', 'Kelas III', 'Kelas IV', 'Kelas V', 'Kelas VI'];

        $data = [];
        foreach ($romawi as $index => $kelas) {
            $data[] = [
                'kdprofile' => 10,
                'statusenabled' => true,
                'kelas' => $kelas, // Format: Kelas I, Kelas II, dst.
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('kelas_m')->insert($data);
    }
}
