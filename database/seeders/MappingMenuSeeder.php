<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\MappingMenu;

class MappingMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ================== DASHBOARD SISWA ==================
        $dashboard = MappingMenu::create([
            'nama_menu' => 'Dashboard Siswa',
            'icon' => 'ri-graduation-cap-line',
            'urutan' => 1,
            'kdprofile' => 10,
            'statusenabled' => true
        ]);

        MappingMenu::insert([
            [
                'nama_menu' => 'Daftar Siswa',
                'parent_id' => $dashboard->id,
                'url' => '/daftar-siswa',
                'urutan' => 1,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Rekap Absensi',
                'parent_id' => $dashboard->id,
                'url' => '/page-absensi',
                'urutan' => 2,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Tap Absensi',
                'parent_id' => $dashboard->id,
                'url' => '/absensi',
                'urutan' => 3,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Input Data',
                'parent_id' => $dashboard->id,
                'url' => '/e-rapot',
                'urutan' => 4,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
        ]);

        // ================== E-RAPOT ==================
        $erapot = MappingMenu::create([
            'nama_menu' => 'E-Rapot',
            'icon' => 'ri-book-read-line',
            'urutan' => 2,
            'kdprofile' => 10,
            'statusenabled' => true
        ]);

        MappingMenu::insert([
            [
                'nama_menu' => 'Preview Rapot',
                'parent_id' => $erapot->id,
                'url' => '/page-absensi',
                'urutan' => 1,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Publish Rapot',
                'parent_id' => $erapot->id,
                'url' => '#',
                'urutan' => 2,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Lihat Rapot Siswa',
                'parent_id' => $erapot->id,
                'url' => '#',
                'urutan' => 3,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
        ]);

        // ================== MASTER DATA ==================
        $masterData = MappingMenu::create([
            'nama_menu' => 'Master Data',
            'icon' => 'ri-database-2-line',
            'urutan' => 3,
            'kdprofile' => 10,
            'statusenabled' => true
        ]);

        MappingMenu::insert([
            [
                'nama_menu' => 'Mata Pelajaran',
                'parent_id' => $masterData->id,
                'url' => '/jadwal/mata-pelajaran',
                'urutan' => 1,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Daftar Pegawai',
                'parent_id' => $masterData->id,
                'url' => '/master/daftar-guru',
                'urutan' => 2,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
        ]);

        // ================== VIEWER ==================
        $viewer = MappingMenu::create([
            'nama_menu' => 'Viewer',
            'icon' => 'ri-pages-line',
            'urutan' => 4,
            'kdprofile' => 10,
            'statusenabled' => true
        ]);

        MappingMenu::create([
            'nama_menu' => 'Tap Absensi',
            'parent_id' => $viewer->id,
            'url' => '/tap-absensi',
            'urutan' => 1,
            'kdprofile' => 10,
            'statusenabled' => true
        ]);

        // ================== SYSTEM ADMIN ==================
        $sysAdmin = MappingMenu::create([
            'nama_menu' => 'System Admin',
            'icon' => 'ri-admin-line',
            'urutan' => 5,
            'kdprofile' => 10,
            'statusenabled' => true
        ]);

        MappingMenu::insert([
            [
                'nama_menu' => 'Daftar Log',
                'parent_id' => $sysAdmin->id,
                'url' => '/sysadmin/daftar-log',
                'urutan' => 1,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Menu Management',
                'parent_id' => $sysAdmin->id,
                'url' => '/sysadmin/menu-management',
                'urutan' => 2,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
            [
                'nama_menu' => 'Login User',
                'parent_id' => $sysAdmin->id,
                'url' => '#',
                'urutan' => 3,
                'kdprofile' => 10,
                'statusenabled' => true
            ],
        ]);
    }
}
