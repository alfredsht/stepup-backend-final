<?php

namespace App\Services;

use App\Models\Admin\Logging;
use App\Models\Admin\JenisLog;
use Illuminate\Support\Facades\Auth;

class LoggingService
{
    /**
     * Simpan log ke database.
     *
     * @param string $namaLog      (Login, Logout, Create, Update, Delete, Error)
     * @param int    $pegawaiId    ID pegawai yang melakukan aksi
     * @param string $keterangan   Keterangan tambahan (opsional)
     * @param string $kdprofile    Default 10
     * @return Logging
     */
    public static function log($namaLog, $pegawaiId = null, $keterangan = null, $kdprofile = '10')
    {
        $jenisLog = JenisLog::where('nama_log', $namaLog)->first();

        if (!$jenisLog) {
            throw new \Exception("Jenis log '$namaLog' belum terdaftar di jenis_log_m");
        }

        if (!$pegawaiId && Auth::check()) {
            $pegawaiId = Auth::id();
        }

        return Logging::create([
            'kdprofile'     => (string)$kdprofile,
            'statusenabled' => true,
            'tanggal_log'   => now(),
            'jenis_log_id'  => $jenisLog->id,
            'pegawai_id'    => $pegawaiId,
            'keterangan'    => $keterangan,
        ]);
    }
}
