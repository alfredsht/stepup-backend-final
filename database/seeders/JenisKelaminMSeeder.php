<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisKelaminMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = ['Laki-laki', 'Perempuan'];

        foreach ($genders as $gender) {
            DB::table('jeniskelamin_m')->insert([
                'kdprofile' => 10,
                'statusenabled' => true,
                'jeniskelamin' => $gender,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
