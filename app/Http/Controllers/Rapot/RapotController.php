<?php

namespace App\Http\Controllers\Rapot;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RapotController extends Controller
{
    use ApiResponse;

    public function getTahunAjaran(Request $request)
    {
        try {
            $hasTanggalMulai = Schema::hasColumn('tahun_ajaran_m', 'tanggal_mulai');
            $hasTanggalSelesai = Schema::hasColumn('tahun_ajaran_m', 'tanggal_selesai');
            $hasIsAktif = Schema::hasColumn('tahun_ajaran_m', 'is_aktif');

            $selects = ['id', 'tahun', 'semester', 'statusenabled'];
            if ($hasTanggalMulai) $selects[] = 'tanggal_mulai';
            if ($hasTanggalSelesai) $selects[] = 'tanggal_selesai';
            if ($hasIsAktif) $selects[] = 'is_aktif';

            $tahunAjaran = DB::table('tahun_ajaran_m')
                ->select($selects)
                ->whereRaw('statusenabled IS TRUE')
                ->orderByDesc('id')
                ->get();

            return $this->successResponse('Data Tahun Ajaran berhasil diambil', [
                'tahun_ajaran' => $tahunAjaran,
            ]);
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function previewRapot(Request $request, $nis)
    {
        try {
            $tahunAjaranId = $request->query('tahun_ajaran_id');

            if (!$tahunAjaranId) {
                return $this->failedResponse('Parameter tahun_ajaran_id wajib diisi', 422);
            }

            $siswa = DB::table('students_m as s')
                ->leftJoin('kelas_m as k', 'k.id', '=', 's.kelasfk')
                ->leftJoin('jeniskelamin_m as jk', 'jk.id', '=', 's.jeniskelaminfk')
                ->select(
                    's.nis',
                    's.namalengkap',
                    's.kelasfk',
                    'k.kelas',
                    'jk.jeniskelamin',
                    's.tanggal_lahir',
                    's.alamat',
                    's.tahun_masuk',
                    's.no_handphone'
                )
                ->where('s.nis', $nis)
                ->whereRaw('s.statusenabled IS TRUE')
                ->first();

            if (!$siswa) {
                return $this->resourceNotFoundResponse('Siswa tidak ditemukan');
            }

            $tahunAjaran = DB::table('tahun_ajaran_m')
                ->select('id', 'tahun', 'semester')
                ->where('id', $tahunAjaranId)
                ->first();

            if ($tahunAjaran && Schema::hasColumn('tahun_ajaran_m', 'tanggal_mulai')) {
                $tahunAjaran->tanggal_mulai = DB::table('tahun_ajaran_m')->where('id', $tahunAjaranId)->value('tanggal_mulai');
            }
            if ($tahunAjaran && Schema::hasColumn('tahun_ajaran_m', 'tanggal_selesai')) {
                $tahunAjaran->tanggal_selesai = DB::table('tahun_ajaran_m')->where('id', $tahunAjaranId)->value('tanggal_selesai');
            }

            if (!$tahunAjaran) {
                return $this->resourceNotFoundResponse('Tahun ajaran tidak ditemukan');
            }

            [$periodStart, $periodEnd] = $this->resolvePeriode($tahunAjaran);

            $nilaiMapel = DB::table('nilai_siswa as n')
                ->join('mapel_m as m', 'm.id', '=', 'n.mapel_id')
                ->leftJoin('detail_nilai_siswa as d', 'd.nilai_siswa_id', '=', 'n.id')
                ->where('n.siswa_nis', $nis)
                ->where('n.tahun_ajaran_id', $tahunAjaranId)
                ->select(
                    'm.id as mapel_id',
                    'm.nama_mapel',
                    'm.nama_singkat',
                    DB::raw('ROUND(AVG(COALESCE(d.nilai_harian, 0)), 2) as rata_harian'),
                    DB::raw('ROUND(AVG(COALESCE(d.nilai_pr, 0)), 2) as rata_pr'),
                    DB::raw('ROUND(AVG(COALESCE(d.nilai_tugas, 0)), 2) as rata_tugas'),
                    DB::raw('ROUND(AVG(COALESCE(d.nilai_uts, 0)), 2) as rata_uts'),
                    DB::raw('ROUND(AVG(COALESCE(d.nilai_uas, 0)), 2) as rata_uas'),
                    DB::raw('ROUND(AVG((COALESCE(d.nilai_harian,0)+COALESCE(d.nilai_pr,0)+COALESCE(d.nilai_tugas,0)+COALESCE(d.nilai_uts,0)+COALESCE(d.nilai_uas,0))/5.0),2) as nilai_akhir'),
                    DB::raw('COUNT(n.id) as jumlah_penilaian')
                )
                ->groupBy('m.id', 'm.nama_mapel', 'm.nama_singkat')
                ->orderBy('m.nama_singkat')
                ->get();

            $rekapAbsensiRaw = DB::table('absensi_m')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->where('objectsiswafk', $nis)
                ->whereBetween('waktu_tap_in', [
                    $periodStart->toDateString() . ' 00:00:00',
                    $periodEnd->toDateString() . ' 23:59:59',
                ])
                ->groupBy('status')
                ->pluck('total', 'status');

            $rekapAbsensi = [
                'tepat_waktu' => (int) ($rekapAbsensiRaw['Tepat waktu'] ?? 0),
                'terlambat' => (int) ($rekapAbsensiRaw['Terlambat'] ?? 0),
                'sakit' => (int) ($rekapAbsensiRaw['Sakit'] ?? 0),
                'izin' => (int) ($rekapAbsensiRaw['Izin'] ?? 0),
                'alpa' => (int) ($rekapAbsensiRaw['Alpa'] ?? 0),
            ];

            $ranking = $this->resolveRanking((int) $tahunAjaranId, (int) $nis, (int) ($siswa->kelasfk ?? 0));

            $rataRataNilai = (float) $nilaiMapel->avg('nilai_akhir');

            $rapotMeta = DB::table('rapot_m')
                ->where('siswa_nis', $nis)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->first();

            $authUser = auth()->user();
            $canManageRapot = $this->canManageRapot($authUser?->id, (int) $siswa->kelasfk);

            return $this->successResponse('Preview rapot berhasil diambil', [
                'siswa' => $siswa,
                'periode' => [
                    'tahun_ajaran_id' => (int) $tahunAjaran->id,
                    'tahun' => $tahunAjaran->tahun,
                    'semester' => $tahunAjaran->semester,
                    'tanggal_mulai' => $periodStart->toDateString(),
                    'tanggal_selesai' => $periodEnd->toDateString(),
                ],
                'nilai_mapel' => $nilaiMapel,
                'rekap_absensi' => $rekapAbsensi,
                'ranking' => $ranking,
                'summary' => [
                    'jumlah_mapel' => $nilaiMapel->count(),
                    'rata_rata_nilai' => round($rataRataNilai, 2),
                ],
                'rapot_meta' => [
                    'catatan_wali_kelas' => $rapotMeta?->catatan_wali_kelas,
                    'is_finalized' => (bool) ($rapotMeta?->is_finalized ?? false),
                    'finalized_at' => $rapotMeta?->finalized_at,
                    'finalized_by' => $rapotMeta?->finalized_by,
                ],
                'permissions' => [
                    'can_manage_rapot' => $canManageRapot,
                ],
                'generated_at' => now('Asia/Jakarta')->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function updateWaliKelasRapot(Request $request, $nis)
    {
        try {
            $validated = $request->validate([
                'tahun_ajaran_id' => 'required|integer|exists:tahun_ajaran_m,id',
                'catatan_wali_kelas' => 'nullable|string|max:5000',
                'is_finalized' => 'nullable|boolean',
            ]);

            $siswa = DB::table('students_m')->select('nis', 'kelasfk')->where('nis', $nis)->first();
            if (!$siswa) {
                return $this->resourceNotFoundResponse('Siswa tidak ditemukan');
            }

            $authUser = auth()->user();
            if (!$this->canManageRapot($authUser?->id, (int) $siswa->kelasfk)) {
                return $this->unauthorizedResponse('Hanya wali kelas yang dapat mengubah catatan rapot');
            }

            $rapot = DB::table('rapot_m')
                ->where('siswa_nis', $nis)
                ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
                ->first();

            if ($rapot && (bool) $rapot->is_finalized) {
                return $this->failedResponse('Rapot sudah finalized dan tidak dapat diubah lagi', 422);
            }

            $finalize = (bool) ($validated['is_finalized'] ?? false);

            $payload = [
                'catatan_wali_kelas' => $validated['catatan_wali_kelas'] ?? null,
                'updated_at' => now('Asia/Jakarta'),
            ];

            if (!$rapot) {
                $payload['kdprofile'] = '10';
                $payload['statusenabled'] = true;
                $payload['siswa_nis'] = $nis;
                $payload['tahun_ajaran_id'] = $validated['tahun_ajaran_id'];
                $payload['is_finalized'] = $finalize;
                $payload['created_at'] = now('Asia/Jakarta');
                if ($finalize) {
                    $payload['finalized_at'] = now('Asia/Jakarta');
                    $payload['finalized_by'] = $authUser?->id;
                }
                DB::table('rapot_m')->insert($payload);
            } else {
                if ($finalize) {
                    $payload['is_finalized'] = true;
                    $payload['finalized_at'] = now('Asia/Jakarta');
                    $payload['finalized_by'] = $authUser?->id;
                }

                DB::table('rapot_m')
                    ->where('id', $rapot->id)
                    ->update($payload);
            }

            return $this->successResponse('Catatan rapot berhasil disimpan');
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    private function canManageRapot($loginUserId, int $kelasId): bool
    {
        if (empty($loginUserId) || empty($kelasId)) {
            return false;
        }

        $waliKelas = DB::table('loginuser_m as lu')
            ->join('pegawai_m as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->where('lu.id', $loginUserId)
            ->whereRaw('pg.statusenabled IS TRUE')
            ->where('pg.is_wali_kelas', true)
            ->where('pg.kelas_wali', $kelasId)
            ->exists();

        return $waliKelas;
    }

    private function resolvePeriode(object $tahunAjaran): array
    {
        if (!empty($tahunAjaran->tanggal_mulai) && !empty($tahunAjaran->tanggal_selesai)) {
            return [
                Carbon::parse($tahunAjaran->tanggal_mulai),
                Carbon::parse($tahunAjaran->tanggal_selesai),
            ];
        }

        $tahun = (string) $tahunAjaran->tahun;
        $semester = strtolower((string) $tahunAjaran->semester);

        preg_match('/(\d{4})\s*\/\s*(\d{4})/', $tahun, $matches);

        if (!empty($matches[1]) && !empty($matches[2])) {
            $startYear = (int) $matches[1];
            $endYear = (int) $matches[2];

            if ($semester === 'genap') {
                return [
                    Carbon::create($endYear, 1, 1),
                    Carbon::create($endYear, 6, 30),
                ];
            }

            return [
                Carbon::create($startYear, 7, 1),
                Carbon::create($startYear, 12, 31),
            ];
        }

        $fallbackYear = now('Asia/Jakarta')->year;

        if ($semester === 'genap') {
            return [
                Carbon::create($fallbackYear, 1, 1),
                Carbon::create($fallbackYear, 6, 30),
            ];
        }

        return [
            Carbon::create($fallbackYear, 7, 1),
            Carbon::create($fallbackYear, 12, 31),
        ];
    }

    private function resolveRanking(int $tahunAjaranId, int $nis, int $kelasId): array
    {
        $base = DB::table('nilai_siswa as n')
            ->join('detail_nilai_siswa as d', 'n.id', '=', 'd.nilai_siswa_id')
            ->join('students_m as s', 's.nis', '=', 'n.siswa_nis')
            ->where('n.tahun_ajaran_id', $tahunAjaranId)
            ->select(
                's.nis',
                's.kelasfk',
                DB::raw('ROUND(AVG((COALESCE(d.nilai_harian,0)+COALESCE(d.nilai_pr,0)+COALESCE(d.nilai_tugas,0)+COALESCE(d.nilai_uts,0)+COALESCE(d.nilai_uas,0))/5.0),2) as rata_rata_nilai')
            )
            ->groupBy('s.nis', 's.kelasfk')
            ->orderByDesc('rata_rata_nilai')
            ->get();

        $globalList = $base->values();
        $kelasList = $base->where('kelasfk', $kelasId)->values();

        $globalRank = null;
        $kelasRank = null;

        foreach ($globalList as $index => $item) {
            if ((int) $item->nis === $nis) {
                $globalRank = $index + 1;
                break;
            }
        }

        foreach ($kelasList as $index => $item) {
            if ((int) $item->nis === $nis) {
                $kelasRank = $index + 1;
                break;
            }
        }

        return [
            'global' => $globalRank,
            'kelas' => $kelasRank,
            'total_global' => $globalList->count(),
            'total_kelas' => $kelasList->count(),
        ];
    }
}

