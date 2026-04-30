<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSentPublic;
use App\Events\MessageSentPublicChannel;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use \Exception;
use App;
use Carbon\Carbon;
use Illuminate\Validation\Rule;


class MasterController extends Controller
{
    use  App\Traits\ApiResponse\ApiResponse;
    use App\Traits\LogingSystems\LogingSystems;

    protected function now()
    {
        return Carbon::now('Asia/Jakarta');
    }
    public function getDataCombo(Request $request)
    {
         $kdprofile = '10';
        try {
            $agama = DB::table('agama_m')
                ->select('id', 'agama')
                ->where('kdprofile', '=', $kdprofile)
                ->get();

            $jeniskelamin = DB::table('jeniskelamin_m')
                ->select('id', 'jeniskelamin')
                ->where('kdprofile', '=', $kdprofile)
                ->get();

            $kelas = DB::table('kelas_m')
                ->select('id', 'kelas')
                ->where('kdprofile', '=', $kdprofile)
                ->orderBy('kelas', 'asc')
                ->get();

            $pekerjaan = DB::table('pekerjaan_m as pk')
                ->select('pk.id', 'pk.pekerjaan')
                ->where('pk.kdprofile', '=', $kdprofile)
                ->limit(5)
                ->get();

            $statusSiswa = DB::table('statussiswa_m')
                ->select('id', 'status_siswa')
                ->where('kdprofile', '=', $kdprofile);

            $pendidikan = DB::table('pendidikan_m')
                ->select('id', 'pendidikan')
                ->where('kdprofile', '=', $kdprofile)
                ->get();

            $st_pegawai = DB::table('statuspegawai_m as sp')
                ->select('sp.id', 'sp.status_pegawai')
                ->where('sp.kdprofile', '=', $kdprofile)
                ->limit(5)
                ->get();

            $statusSiswa = $statusSiswa->get();

            $alamat = DB::table('desakelurahan_m as ds')
                ->select('ds.id as desakel_id', 'ds.desakelurahan', 'kc.kecamatan', 'kab.kabupatenkota', 'prov.provinsi', 'ds.kodepos', 'ds.objectkecamatanfk as kec_id', 'ds.objectkabupatenfk as kab_id', 'ds.objectprovinsi as prov_id')
                ->join('kecamatan_m as kc', 'ds.objectkecamatanfk', '=', 'kc.id')
                ->join('kabupatenkota_m as kab', 'ds.objectkabupatenfk', '=', 'kab.id')
                ->join('provinsi_m as prov', 'ds.objectprovinsi', '=', 'prov.id')
                ->whereRaw('ds.statusenabled IS TRUE')
                ->where('ds.kdprofile', '=', $kdprofile)
                ->orderBy('ds.desakelurahan');
            $alamat = $alamat->take(20);
            $alamat = $alamat->get();

            $response = [
                'agama' => $agama,
                'jeniskelamin' => $jeniskelamin,
                'kelas' => $kelas,
                'st_pegawai' => $st_pegawai,
                'pendidikan' => $pendidikan,
                'pekerjaan' => $pekerjaan,
                'statussiswa' => $statusSiswa,

            ];

            if ($response) {
                return $this->successResponse('Data Combo retrieved successfully', $response);
            } else {
                return $this->failedResponse('Failed to retrieve Data Combo');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function getDataComboProv(Request $request)
    {
         $kdprofile = '10';
        try {
            $provinsi = DB::table('provinsi_m')
                ->select('id as prov_id', 'provinsi')
                ->where('kdprofile', '=', $kdprofile)
                ->get();

            $response = [
                'provinsi' => $provinsi
            ];

            return response()->json(['provinsi' => $provinsi], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDataComboKabupaten(Request $request, $provinsi_id)
    {
        try {
            $kabupaten = DB::table('kabupatenkota_m')
                ->select('id', 'kabupatenkota as kabkota')
                ->where('objectprovinsifk', '=', $provinsi_id)
                ->get();

            if ($kabupaten->isEmpty()) {
                return response()->json(['message' => 'Data kabupaten/kota tidak ditemukan', 'kabupaten' => []], 404);
            }

            return response()->json(['kabupaten' => $kabupaten], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDataComboKecamatan(Request $request, $kabupaten_id)
    {
        try {
            $kecamatan = DB::table('kecamatan_m')
                ->select('id', 'kecamatan')
                ->where('objectkabupatenkotafk', '=', $kabupaten_id)
                ->get();

            if ($kecamatan->isEmpty()) {
                return response()->json(['message' => 'Data kecamatan tidak ditemukan', 'kecamatan' => []], 404);
            }

            return response()->json(['kecamatan' => $kecamatan], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDataComboKelurahan(Request $request, $kecamatan_id)
    {
        try {
            $kelurahan = DB::table('desakelurahan_m')
                ->select('id', 'desakelurahan as kelurahan')
                ->where('objectkecamatanfk', '=', $kecamatan_id)
                ->get();

            if ($kelurahan->isEmpty()) {
                return response()->json(['message' => 'Data kelurahan tidak ditemukan', 'kelurahan' => []], 404);
            }

            return response()->json(['kelurahan' => $kelurahan], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDataComboPegawai(Request $request)
    {
         $kdprofile = '10';
        try {
            $pegawai = DB::table('pegawai_m')
                ->select('id', 'namalengkap')
                ->where('kdprofile', '=', $kdprofile)
                ->get();

            $response = [
                'pegawai' => $pegawai,
            ];

            if ($response) {
                return $this->successResponse('Data Combo Guru retrieved successfully', $response);
            } else {
                return $this->failedResponse('Failed to retrieve Data Combo Guru');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function getDataRekapTap(Request $request)
    {
         $kdprofile = '10';
        $request->validate([
            'tglAwal' => 'required|date',
            'tglAkhir' => 'required|date',
        ]);
        try {
            $RekapTap = DB::table('kelas_m as ks')
                ->select(
                    'ks.kelas',
                    DB::raw('COUNT(st.nis) as total_siswa'),
                    DB::raw('COUNT(DISTINCT abs.objectsiswafk) as jumlah_absen')
                )
                ->leftJoin('students_m as st', 'ks.id', '=', 'st.kelasfk')
                ->leftJoin('absensi_m as abs', function ($join) use ($kdprofile, $request) {
                    $join->on('st.nis', '=', 'abs.objectsiswafk')
                        ->where('abs.kdprofile', '=', $kdprofile)
                        ->whereBetween('abs.waktu_tap_in', [$request->tglAwal, $request->tglAkhir])
                        ->whereRaw('abs.statusenabled IS TRUE');
                })
                ->where('ks.kdprofile', '=', $kdprofile)
                ->groupBy('ks.kelas')
                ->get();


            $response = [
                'rekap' => $RekapTap,
            ];
            if ($response) {
                return $this->successResponse('Data Rekap retrieved successfully', $response);
            } else {
                return $this->failedResponse('Failed to retrieve Data Rekap');
            }
        } catch (\Error $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function test()
    {
        set_time_limit(0);
        $data = ['name' => 'John Doe', 'email' => 'john.doe@example.com'];
        MessageSentPublicChannel::dispatch('Updated');
        return "Event sent";
    }

    public function getDataRekapCard(Request $request)
    {
         $kdprofile = '10';
        $request->validate([
            'tglAwal' => 'required|date',
            'tglAkhir' => 'required|date',
        ]);
        try {
            $absensi = DB::table('students_m as sm')
                ->leftJoin('absensi_m as am', 'am.objectsiswafk', '=', 'sm.nis')
                ->join('kelas_m as kl', 'kl.id', '=', 'sm.kelasfk')
                ->select(
                    'sm.nis',
                    DB::raw("CASE WHEN am.status IS NULL THEN '-' ELSE am.status END AS status_absensi"),
                    DB::raw("CASE WHEN am.waktu_tap_in IS NULL THEN '-' ELSE TO_CHAR(am.waktu_tap_in, 'YYYY-MM-DD HH24:MI:SS') END AS waktu_tap")
                )
                ->where('sm.kdprofile', '10')
                ->whereRaw('sm.statusenabled IS TRUE')
                ->orderBy('am.waktu_tap_in', 'ASC')
                ->get();

            if (!$absensi) {
                return $this->resourceNotFoundResponse('Data absensi tidak ditemukan.');
            }

            if ($absensi) {
                return $this->successCreatedResponse('Fetched data absensi successfull', $absensi);
            } else {
                return $this->failedResponse('Failed to fetched absensi.');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
    public function getMapel(Request $request)
    {
         $kdprofile = '10';
        try {
            $mapel = DB::table('mapel_m')
                ->select('id', 'statusenabled', 'nama_mapel', 'kode_mapel', 'nama_singkat', 'deskripsi')
                ->where('kdprofile', '=', $kdprofile)
                ->where('deleted_at', '=', null)
                ->orderBy('nama_mapel', 'asc')
                ->get();

            if ($mapel->isEmpty()) {
                return response()->json(['message' => 'Data mata pelajaran tidak ditemukan', 'mapel' => []], 404);
            }
            return response()->json(['data' => $mapel], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getDataMapelChart(Request $request)
    {
        $nis = $request->input('nis');
        $tahunAjaranId = $request->input('tahun_ajaran_id');

        if (!$nis) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter nis wajib diisi',
            ], 400);
        }

        $data = DB::table('mapel_m as m')
            ->leftJoin('nilai_siswa as n', function ($join) use ($nis) {
                $join->on('m.id', '=', 'n.mapel_id')
                    ->where('n.siswa_nis', '=', $nis);
            })
            ->leftJoin('detail_nilai_siswa as d', 'n.id', '=', 'd.nilai_siswa_id')
            ->select(
                'm.nama_mapel',
                'm.nama_singkat',
                DB::raw("
                    ROUND(
                        AVG(
                        CASE 
                            WHEN d.id IS NULL THEN NULL
                            ELSE (
                            COALESCE(d.nilai_harian,0)
                            + COALESCE(d.nilai_pr,0)
                            + COALESCE(d.nilai_tugas,0)
                            + COALESCE(d.nilai_uts,0)
                            + COALESCE(d.nilai_uas,0)
                            ) / 5.0
                        END
                        ), 2
                    ) AS rata_rata_nilai
                    ")
            )
            ->whereRaw('m.statusenabled IS TRUE')
            ->whereRaw('n.statusenabled IS TRUE')
            ->groupBy('m.nama_mapel', 'm.nama_singkat')
            ->orderBy('m.nama_singkat', 'asc')
            ->get();

        if (!empty($tahunAjaranId)) {
            $data = DB::table('mapel_m as m')
                ->leftJoin('nilai_siswa as n', function ($join) use ($nis, $tahunAjaranId) {
                    $join->on('m.id', '=', 'n.mapel_id')
                        ->where('n.siswa_nis', '=', $nis)
                        ->where('n.tahun_ajaran_id', '=', $tahunAjaranId);
                })
                ->leftJoin('detail_nilai_siswa as d', 'n.id', '=', 'd.nilai_siswa_id')
                ->select(
                    'm.nama_mapel',
                    'm.nama_singkat',
                    DB::raw("
                    ROUND(
                        AVG(
                        CASE 
                            WHEN d.id IS NULL THEN NULL
                            ELSE (
                            COALESCE(d.nilai_harian,0)
                            + COALESCE(d.nilai_pr,0)
                            + COALESCE(d.nilai_tugas,0)
                            + COALESCE(d.nilai_uts,0)
                            + COALESCE(d.nilai_uas,0)
                            ) / 5.0
                        END
                        ), 2
                    ) AS rata_rata_nilai
                    ")
                )
                ->whereRaw('pm.statusenabled IS TRUE')
                ->groupBy('m.nama_mapel', 'm.nama_singkat')
                ->orderBy('m.nama_singkat', 'asc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function getDataMapel(Request $request)
    {
        $data = DB::table('mapel_m')
            ->where('deleted_at', null)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function postMataPelajaran(Request $request)
    {
         $kdprofile = '10';
        $validated = $request->validate([
            'status' => 'required|in:aktif,tidak',
            'kode_mapel' => 'required|string|max:10|unique:mapel_m,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'nama_singkat' => 'nullable|string|max:50',
        ]);
        try {
            $statusEnabled = $validated['status'] === 'aktif';
            $postMapel = DB::table('mapel_m')->insert([
                'statusenabled' => DB::raw($statusEnabled ? 'true' : 'false'),
                'kdprofile' => $kdprofile,
                'kode_mapel' => $validated['kode_mapel'],
                'nama_mapel' => $validated['nama_mapel'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'nama_singkat' => $validated['nama_singkat'] ?? null,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);

            $changes = [
                "Tambah mata pelajaran '{$validated['nama_mapel']}' dengan kode {$validated['kode_mapel']}"
            ];

            if (!empty($changes)) {
                create_log(3, json_encode($changes, JSON_UNESCAPED_UNICODE));
            } else {
                create_log(4, json_encode(["Tidak ada perubahan pada mata pelajaran kode {$validated['kode_mapel']}"], JSON_UNESCAPED_UNICODE));
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => $postMapel
                ],
                200
            );
        } catch (\Exception $e) {
            create_log(6, json_encode(["Error tambah mata pelajaran kode {$validated['kode_mapel']} : " . $e->getMessage()], JSON_UNESCAPED_UNICODE));
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateMataPelajaran(Request $request, $id)
    {
         $kdprofile = '10';
        $existingMapel = DB::table('mapel_m')
            ->where('kode_mapel', $id)
            ->where('kdprofile', $kdprofile)
            ->first();

        if (!$existingMapel) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $updateData = [];


        if ($request->has('deleted') && $request->input('deleted') === true) {
            $updateData['deleted_at'] = $this->now();
            $updateData['statusenabled'] = false;
        } else {

            $rules = [
                'status' => 'required|in:aktif,tidak',
                'kode_mapel' => [
                    'required',
                    'string',
                    'max:10',
                    Rule::unique('mapel_m', 'kode_mapel')->ignore($id, 'kode_mapel')
                ],
                'nama_mapel' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
                'nama_singkat' => 'nullable|string|max:50',
            ];
            $validated = $request->validate($rules);
            $statusEnabled = $validated['status'] === 'aktif';

            $updateData = [
                'statusenabled' => $statusEnabled,
                'kode_mapel' => $validated['kode_mapel'],
                'nama_mapel' => $validated['nama_mapel'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'nama_singkat' => $validated['nama_singkat'] ?? null,
                'updated_at' => $this->now(),
            ];
        }

        try {
            $dbUpdateData = $updateData;
            if (array_key_exists('statusenabled', $dbUpdateData)) {
                $dbUpdateData['statusenabled'] = DB::raw($dbUpdateData['statusenabled'] ? 'true' : 'false');
            }

            DB::table('mapel_m')
                ->where('kode_mapel', $id)
                ->update($dbUpdateData);

            $ignoreFields = ['updated_at', 'kdprofile'];
            $fieldLabels = [
                'nama_mapel'   => 'Nama Mapel',
                'deskripsi'    => 'Deskripsi',
                'nama_singkat' => 'Nama Singkat',
                'statusenabled' => 'Status',
                'kode_mapel'   => 'Kode Mapel',
            ];

            $changes = [];
            foreach ($updateData as $key => $newValue) {
                if (in_array($key, $ignoreFields)) {
                    continue;
                }

                $oldValue = $existingMapel->$key ?? null;

                // translate status biar lebih manusiawi
                if ($key === 'statusenabled') {
                    $oldValue = $oldValue == 1 ? 'Aktif' : 'Tidak aktif';
                    $newValue = $newValue == 1 ? 'Aktif' : 'Tidak aktif';
                }

                // kalau null/empty tampilkan "kosong"
                $oldValueText = $oldValue === null || $oldValue === '' ? 'kosong' : $oldValue;
                $newValueText = $newValue === null || $newValue === '' ? 'kosong' : $newValue;

                if ($oldValueText != $newValueText) {
                    $label = $fieldLabels[$key] ?? $key;
                    $changes[] = "Update $label Mapel dari '$oldValueText' ke '$newValueText' dengan kode $id";
                }
            }

            if ($request->has('deleted') && $request->boolean('deleted')) {
                create_log(5, json_encode(["Hapus mata pelajaran dengan kode $id"], JSON_UNESCAPED_UNICODE));
            } elseif (!empty($changes)) {
                // simpan langsung sebagai JSON array
                create_log(4, json_encode($changes, JSON_UNESCAPED_UNICODE));
            } else {
                create_log(4, json_encode(["Tidak ada perubahan pada mata pelajaran kode $id"], JSON_UNESCAPED_UNICODE));
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
            ], 200);
        } catch (\Exception $e) {
            create_log(6, json_encode(["Error update mata pelajaran kode $id : " . $e->getMessage()], JSON_UNESCAPED_UNICODE));

            return response()->json([
                'error' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getDataPegawai(Request $request)
    {
         $kdprofile = '10';
        try {
            $pegawai = DB::table('pegawai_m as pm')
                ->select(
                    'pm.id',
                    'pm.namalengkap',
                    'pm.nip',
                    'pm.nik',
                    'pm.tempat_lahir',
                    'pm.nuptk',
                    'pm.sk_pengangkatan',
                    'pm.status_kepegawaian',
                    'pm.agamafk',
                    'pm.no_handphone',
                    'pm.tahun_masuk',
                    'pm.jeniskelaminfk',
                    'pm.no_handphone',
                    'pm.tahun_masuk',
                    'pm.objectpendidikanterakhirfk',
                    'pm.objectnegarafk',
                    'pm.tmt_pengangkatan',
                    'pm.tmt_jabatan',
                    'pm.kelas_wali',
                    'pm.alamat',
                    'pm.tanggal_lahir',
                    'pm.status_kepegawaian as st_pegawai',
                    'pm.is_aktif',
                    'sp.status_pegawai as statuspegawai',
                    'pm.foto',
                )
                ->leftJoin('agama_m as ag', 'pm.agamafk', '=', 'ag.id')
                ->leftjoin('jeniskelamin_m as jk', 'pm.jeniskelaminfk', '=', 'jk.id')
                ->leftjoin('statuspegawai_m as sp', 'pm.status_kepegawaian', '=', 'sp.id')
                ->leftjoin('pendidikan_m as pd', 'pm.objectpendidikanterakhirfk', '=', 'pd.id')
                ->where('pm.kdprofile', '=', $kdprofile)
                ->whereRaw('pm.statusenabled IS TRUE')
                ->get();

            $response = [
                'pegawai' => $pegawai,
            ];

            if ($response) {
                return $this->successResponse('Data Guru retrieved successfully', $response);
            } else {
                return $this->failedResponse('Failed to retrieve Data Combo Guru');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
    public function postDataPegawai(Request $request)
    {
         $kdprofile = '10';
        try {
            DB::table('pegawai_m')->insert([
                'kdprofile' => $kdprofile,
                'is_aktif' => 1,
                'statusenabled' => true,
                'namalengkap' => $request->input('namalengkap'),
                'nip' => $request->input('nip'),
                'nik' => $request->input('nik'),
                'nuptk' => $request->input('nuptk'),
                'sk_pengangkatan' => $request->input('sk_pengangkatan'),
                'tempat_lahir' => $request->input('tempat_lahir'),
                'no_handphone' => $request->input('no_handphone'),
                'tahun_masuk' => $request->input('tahun_masuk'),
                'agamafk' => $request->input('agamafk'),
                'objectjenispegawaifk' => 2,
                'kelasfk' => 1,
                'objectnegarafk' => $request->input('warga_negara'),
                'alamat' => $request->input('alamat'),
                'jeniskelaminfk' => $request->input('jeniskelaminfk'),
                'tmt_pengangkatan' => $request->input('tmt_pengangkatan'),
                'tmt_jabatan' => $request->input('tmt_jabatan'),
                'status_kepegawaian' => $request->input('status_kepegawaian'),
                'objectpendidikanterakhirfk' => $request->input('pendidikan_terakhirfk'),
                'foto' => $request->input('foto'),
                'kelas_wali' => $request->input('kelas_wali'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'message' => 'Data pegawai berhasil ditambahkan.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan data pegawai.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateDataPegawai(Request $request, $id)
    {
         $kdprofile = '10';
        if ($request->has('status')) {
            $validated = $request->validate([
                'status' => 'required|in:aktif,tidak',
            ]);

            try {
                $pegawai = DB::table('pegawai_m')
                    ->where('id', $id)
                    ->where('kdprofile', $kdprofile)
                    ->first();

                if (!$pegawai) {
                    return response()->json(['error' => 'Data pegawai tidak ditemukan'], 404);
                }

                $updateData = [
                    'is_aktif' => $validated['status'] === 'aktif' ? 1 : 0,
                    'updated_at' => now(),
                ];

                DB::table('pegawai_m')
                    ->where('id', $id)
                    ->update($updateData);

                $logEditStatus = [
                    "Ubah status pegawai $pegawai->namalengkap dari '" . ($pegawai->is_aktif ? 'Aktif' : 'Tidak aktif') . "' menjadi '" . ($updateData['is_aktif'] ? 'Aktif' : 'Tidak aktif') . "' dengan id pegawai $id"
                ];

                create_log(4, json_encode($logEditStatus, JSON_UNESCAPED_UNICODE));

                return response()->json([
                    'success' => true,
                    'message' => 'Status pegawai berhasil diperbarui',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }
        } elseif ($request->has('delete')) {
            try {
                $pegawai = DB::table('pegawai_m')
                    ->where('id', $id)
                    ->where('kdprofile', $kdprofile)
                    ->first();

                if (!$pegawai) {
                    return response()->json(['error' => 'Data pegawai tidak ditemukan'], 404);
                }

                DB::table('pegawai_m')
                    ->where('id', $id)
                    ->where('kdprofile', $kdprofile)
                    ->update([
                        'statusenabled' => false,
                        'updated_at' => now()
                    ]);

                $logDeletePegawai = [
                    "Delete pegawai '$pegawai->namalengkap' dengan id pegawai $id"
                ];

                create_log(5, json_encode($logDeletePegawai, JSON_UNESCAPED_UNICODE));

                return response()->json([
                    'success' => true,
                    'message' => 'Data pegawai berhasil dihapus',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }
        } else {
            $validated = $request->validate([
                'namalengkap' => 'nullable|string',
                'nip' => 'nullable|string',
                'nik' => 'nullable|string',
                'nuptk' => 'nullable|string',
                'sk_pengangkatan' => 'nullable|string',
                'tempat_lahir' => 'nullable|string',
                'no_handphone' => 'nullable|string',
                'tahun_masuk' => 'nullable|integer',
                'agamafk' => 'nullable|integer',
                'jeniskelaminfk' => 'nullable|integer',
                'pendidikan_terakhirfk' => 'nullable|integer',
                'warga_negara' => 'nullable|integer',
                'alamat' => 'nullable|string',
                'tmt_pengangkatan' => 'nullable|date',
                'tmt_jabatan' => 'nullable|date',
                'kelas' => 'nullable|integer',
                'kelas_wali' => 'nullable|integer',
                'status_kepegawaian' => 'nullable|integer',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:800',
            ]);

            try {
                $pegawai = DB::table('pegawai_m')
                    ->where('id', $id)
                    ->where('kdprofile', $kdprofile)
                    ->first();

                if (!$pegawai) {
                    return response()->json(['error' => 'Data pegawai tidak ditemukan'], 404);
                }

                $existingPegawai = DB::table('pegawai_m')->where('id', $id)->first();

                if ($request->hasFile('foto')) {
                    $foto = $request->file('foto');

                    // Upload ke Cloudinary (pakai Facade)
                    $uploadResult = Cloudinary::uploadFile($foto->getRealPath(), [
                        'folder' => 'foto_pegawai',
                        'public_id' => uniqid('pegawai_')
                    ]);

                    // Simpan secure URL ke DB
                    $fotoPath = $uploadResult->getSecurePath();

                    // Hapus foto lama kalau ada
                    if (!empty($existingPegawai->foto)) {
                        $parsedUrl = parse_url($existingPegawai->foto);
                        $pathParts = explode('/', ltrim($parsedUrl['path'], '/'));
                        $publicIdWithExt = end($pathParts);
                        $oldPublicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);

                        Cloudinary::destroy('foto_pegawai/' . $oldPublicId);
                    }
                } elseif (empty($request->input('foto'))) {
                    if (!empty($existingPegawai->foto)) {
                        $parsedUrl = parse_url($existingPegawai->foto);
                        $pathParts = explode('/', ltrim($parsedUrl['path'], '/'));
                        $publicIdWithExt = end($pathParts);
                        $publicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);

                        Cloudinary::destroy('foto_pegawai/' . $publicId);
                    }

                    $fotoPath = null;
                } else {
                    $fotoPath = $existingPegawai->foto ?? null;
                }

                $updateData = [
                    'namalengkap' => $validated['namalengkap'],
                    'nip' => $validated['nip'] ?? null,
                    'nik' => $validated['nik'] ?? null,
                    'nuptk' => $validated['nuptk'] ?? null,
                    'sk_pengangkatan' => $validated['sk_pengangkatan'] ?? null,
                    'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                    'no_handphone' => $validated['no_handphone'] ?? null,
                    'tahun_masuk' => $validated['tahun_masuk'] ?? null,
                    'agamafk' => $validated['agamafk'] ?? null,
                    'jeniskelaminfk' => $validated['jeniskelaminfk'] ?? null,
                    'objectpendidikanterakhirfk' => $validated['pendidikan_terakhirfk'] ?? null,
                    'objectnegarafk' => $validated['warga_negara'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                    'tmt_pengangkatan' => $validated['tmt_pengangkatan'] ?? null,
                    'tmt_jabatan' => $validated['tmt_jabatan'] ?? null,
                    'kelasfk' => $validated['kelas'] ?? 1,
                    'kelas_wali' => $validated['kelas_wali'],
                    'status_kepegawaian' => $validated['status_kepegawaian'] ?? null,
                    'foto' => $fotoPath,
                    'updated_at' => now(),
                ];

                DB::table('pegawai_m')
                    ->where('id', $id)
                    ->update($updateData);

                // LOGGING perubahan detail pegawai
                $fieldLabels = [
                    'namalengkap' => 'Nama Lengkap',
                    'nip' => 'NIP',
                    'nik' => 'NIK',
                    'nuptk' => 'NUPTK',
                    'sk_pengangkatan' => 'SK Pengangkatan',
                    'tempat_lahir' => 'Tempat Lahir',
                    'no_handphone' => 'No Handphone',
                    'tahun_masuk' => 'Tahun Masuk',
                    'agamafk' => 'Agama',
                    'jeniskelaminfk' => 'Jenis Kelamin',
                    'objectpendidikanterakhirfk' => 'Pendidikan Terakhir',
                    'objectnegarafk' => 'Warga Negara',
                    'alamat' => 'Alamat',
                    'tmt_pengangkatan' => 'TMT Pengangkatan',
                    'tmt_jabatan' => 'TMT Jabatan',
                    'kelasfk' => 'Kelas',
                    'kelas_wali' => 'Kelas Wali',
                    'status_kepegawaian' => 'Status Kepegawaian',
                    'foto' => 'Foto',
                ];

                $changedFields = [];
                foreach ($updateData as $key => $newValue) {
                    $oldValue = $pegawai->$key ?? null;
                    $oldValueText = $oldValue === null || $oldValue === '' ? 'kosong' : $oldValue;
                    $newValueText = $newValue === null || $newValue === '' ? 'kosong' : $newValue;

                    if ($oldValueText != $newValueText) {
                        $changedFields[] = $fieldLabels[$key] ?? $key;
                    }
                }

                if (!empty($changedFields)) {
                    $logMessage = "Update data pegawai '{$pegawai->namalengkap}' (id: $id), field yang diubah: " . implode(', ', $changedFields);
                    create_log(4, json_encode([$logMessage], JSON_UNESCAPED_UNICODE));
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data pegawai berhasil diperbarui',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    public function getDataPegawaiById(Request $request, $pegawaiId)
    {
         $kdprofile = '10';
        try {
            $pegawai = DB::table('pegawai_m as pm')
                ->select(
                    'pm.id',
                    'pm.namalengkap',
                    'pm.nip',
                    'pm.nik',
                    'pm.tempat_lahir',
                    'pm.agamafk',
                    'pm.jeniskelaminfk',
                    'pm.objectpendidikanterakhirfk',
                    'pm.status_kepegawaian',
                    'ag.agama',
                    'pm.no_handphone',
                    'pm.tahun_masuk',
                    'jk.jeniskelamin',
                    'pd.pendidikan',
                    'pm.objectnegarafk',
                    'pm.tmt_pengangkatan',
                    'pm.jabatan',
                    'pm.tempat_lahir',
                    'pm.kelas_wali',
                    'pm.alamat',
                    'pm.tanggal_lahir',
                    'pm.status_kepegawaian as st_pegawai',
                    'pm.is_aktif',
                    'sp.status_pegawai as statuspegawai',
                    'pm.email',
                    'pm.foto'
                )
                ->leftJoin('agama_m as ag', 'pm.agamafk', '=', 'ag.id')
                ->leftJoin('jeniskelamin_m as jk', 'pm.jeniskelaminfk', '=', 'jk.id')
                ->leftJoin('statuspegawai_m as sp', 'pm.status_kepegawaian', '=', 'sp.id')
                ->leftJoin('pendidikan_m as pd', 'pm.objectpendidikanterakhirfk', '=', 'pd.id')
                ->where('pm.kdprofile', $kdprofile)
                ->where('pm.id', $pegawaiId)
                ->first();

            if ($pegawai) {
                return $this->successResponse('Data Guru retrieved successfully', [
                    'pegawai' => $pegawai
                ]);
            } else {
                return $this->failedResponse('Pegawai tidak ditemukan');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
    public function getDataComboJenisTransaksi(Request $request)
    {
         $kdprofile = '10';
        try {
            $transaksi = DB::table('jenis_transaksi_tabungan')
                ->select('id', 'kode', 'nama')
                ->where('kdprofile', $kdprofile)
                ->get();

            if ($transaksi->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan',
                    'data'    => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil diambil',
                'data'    => $transaksi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getRankingSiswa(Request $request)
    {
        try {
            $kelasId = $request->query('kelas_id');
            $tahunAjaranId = $request->query('tahun_ajaran_id');

            if ($kelasId === null || $tahunAjaranId === null) {
                throw new \Exception('kelas_id dan tahun_ajaran_id wajib diisi', 400);
            }

            $query = DB::table('nilai_siswa as n')
                ->join('detail_nilai_siswa as d', 'n.id', '=', 'd.nilai_siswa_id')
                ->join('students_m as s', 'n.siswa_nis', '=', 's.nis')
                ->where('n.tahun_ajaran_id', $tahunAjaranId)
                ->select(
                    's.nis',
                    's.namalengkap',
                    's.kelasfk',
                    's.foto',
                    DB::raw('ROUND(AVG((COALESCE(d.nilai_harian,0)+COALESCE(d.nilai_pr,0)+COALESCE(d.nilai_tugas,0)+COALESCE(d.nilai_uts,0)+COALESCE(d.nilai_uas,0))/5.0),2) as rata_rata_nilai')
                )
                ->groupBy('s.nis', 's.namalengkap', 's.kelasfk', 's.foto')
                ->orderByDesc('rata_rata_nilai');

            if ($kelasId == 0) {
                $ranking = $query->get();
            } else {
                $ranking = $query->where('s.kelasfk', $kelasId)->get();
            }

            $ranking = $ranking->map(function ($item, $index) {
                $item->ranking = $index + 1;
                $item->foto = !empty($item->foto)
                    ? Cloudinary::getUrl($item->foto)
                    : null;
                return $item;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data'    => $ranking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getDataComboJenisPegawai(Request $request)
    {
         $kdprofile = '10';
        try {
            $jenispegawai = DB::table('jenispegawai_m')
                ->select('id', 'jenispegawai as jenis_pegawai')
                ->where('kdprofile', '=', $kdprofile)
                ->get();

            $response = [
                'jenis_pegawai' => $jenispegawai,
            ];

            if ($response) {
                return $this->successResponse('Data Combo Jenis Pegawai retrieved successfully', $response);
            } else {
                return $this->failedResponse('Failed to retrieve Data Combo Jenis Pegawai');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
}



