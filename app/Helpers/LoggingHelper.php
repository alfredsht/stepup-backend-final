<?php

use App\Models\Admin\Logging;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

if (!function_exists('create_log')) {
    /**
     * Buat log baru
     *
     * @param int $jenisLogId
     * @param string|null $keterangan
     * @param int|null $pegawaiId
     * @param int $kdprofile
     * @return void
     */
    function create_log(int $jenisLogId, ?string $keterangan = null, ?int $pegawaiId = null, int $kdprofile = 10): void
    {
        try {
            if ($pegawaiId === 0) {
                $pegawaiId = null;
            } elseif (!$pegawaiId) {
                try {
                    $user = auth('api')->user();
                    $pegawaiId = $user->objectpegawaifk ?? null;
                } catch (\Throwable $e) {
                    Log::warning('Auth gagal: ' . $e->getMessage());
                    $pegawaiId = null;
                }
            }

            Logging::create([
                'kdprofile'     => $kdprofile,
                'statusenabled' => true,
                'tanggal_log'   => now(),
                'jenis_log_id'  => $jenisLogId,
                'pegawai_id'    => $pegawaiId,
                'keterangan'    => $keterangan,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat log: ' . $e->getMessage());
        }
    }
}
