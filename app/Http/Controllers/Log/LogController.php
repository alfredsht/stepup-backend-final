<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Models\Admin\Logging;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function getLogData(Request $request)
    {
        $query = Logging::query()
            ->with(['jenisLog:id,nama_log', 'pegawai:id,namalengkap'])
            ->orderBy('tanggal_log', 'desc');


        if ($request->filled('jenis_log_id')) {
            $query->where('jenis_log_id', $request->jenis_log_id);
        }

        if ($request->filled('pegawai_id')) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->filled('kdprofile')) {
            $query->where('kdprofile', $request->kdprofile);
        }

        $logs = $query->paginate($request->get('per_page', 20));


        $data = $logs->getCollection()->map(function ($log) {
            return [
                'tanggal_log' => $log->tanggal_log,
                'user'        => $log->pegawai->namalengkap ?? 'System',
                'jenis_log'   => $log->jenisLog->nama_log ?? '-',
                'keterangan'  => $log->keterangan,
            ];
        });

        return response()->json([
            'current_page' => $logs->currentPage(),
            'per_page'     => $logs->perPage(),
            'total'        => $logs->total(),
            'data'         => $data,
        ]);
    }
}
