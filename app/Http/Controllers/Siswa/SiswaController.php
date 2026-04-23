<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Exception;
use App\Events\MessageSentPublicChannel;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;
use App\Http\Controllers\Master\NotificationController;
use Illuminate\Support\Facades\Log;
use App;

class SiswaController extends Controller
{
    use  App\Traits\ApiResponse\ApiResponse;
    use App\Traits\LogingSystems\LogingSystems;
    protected function now()
    {
        return Carbon::now('Asia/Jakarta');
    }
    public function getDataSiswa()
    {
        try {
            $data = DB::table('students_m as st')
                ->join('kelas_m as kl', 'kl.id', '=', 'st.kelasfk')
                ->join('jeniskelamin_m as jk', 'jk.id', '=', 'st.jeniskelaminfk')
                ->join('agama_m as ag', 'ag.id', '=', 'st.agamafk')
                ->leftJoin('pekerjaan_m as pk1', 'pk1.id', '=', 'st.pekerjaanayahfk')
                ->leftJoin('statussiswa_m as ss', 'ss.id', '=', 'st.statussiswafk')
                ->leftJoin('pekerjaan_m as pk2', 'pk2.id', '=', 'st.pekerjaanibufk')
                ->leftJoin('provinsi_m as pm', 'pm.id', '=', 'st.provinsifk')
                ->select(
                    'st.nis',
                    'st.nisn',
                    'st.namalengkap',
                    'st.tanggal_lahir',
                    'st.tempat_lahir',
                    'kl.kelas',
                    'st.foto',
                    'ss.status_siswa',
                    'st.statussiswafk',
                    'st.kelasfk',
                    DB::raw("CASE WHEN st.jeniskelaminfk = 1 THEN 'L' WHEN st.jeniskelaminfk = 2 THEN 'P' ELSE '-' END AS jeniskelamin"),
                    'ag.agama',
                    'pm.provinsi',
                    'st.alamat',
                    'st.provinsifk',
                    'st.kabkotafk',
                    'st.kecamatanfk',
                    'st.kelurahanfk',
                    'st.no_handphone',
                    'st.nama_ayah',
                    'st.nama_ibu',
                    'pk1.pekerjaan as pekerjaan_ayah',
                    'pk2.pekerjaan as pekerjaan_ibu',
                    'st.tahun_masuk',
                    'st.nfc_tag_id',
                    'st.pekerjaanayahfk',
                    'st.pekerjaanibufk'
                )
                ->where('st.kdprofile', 10)
                ->where('st.statusenabled', true)
                ->orderBy('st.namalengkap', 'ASC')
                ->get();

            // Gunakan map() untuk koleksi Laravel
            $data = $data->map(function ($item) {
                $item->foto = !empty($item->foto) ? Cloudinary::getUrl($item->foto) : null;
                return $item;
            });

            if ($data->isNotEmpty()) {
                return $this->successResponse('Get Data Siswa Berhasil', $data);
            } else {
                return $this->failedResponse('Gagal Get Data Siswa');
            }
        } catch (\Throwable $e) {
            return $this->failedResponse($e->getMessage());
        }
    }
    public function getDataSiswaAbsensiByKelas(Request $request)
    {
        try {
            $data = DB::table('students_m as st')
                ->join('kelas_m as kl', 'kl.id', '=', 'st.kelasfk')
                ->join('jeniskelamin_m as jk', 'jk.id', '=', 'st.jeniskelaminfk')
                ->join('agama_m as ag', 'ag.id', '=', 'st.agamafk')
                ->leftJoin('absensi_m as ab', function ($join) {
                    $join->on('ab.objectsiswafk', '=', 'st.nis')
                        ->whereDate('ab.waktu_tap_in', $this->now()->toDateString());
                })
                ->leftJoin('pekerjaan_m as pk1', 'pk1.id', '=', 'st.pekerjaanayahfk')
                ->leftJoin('pekerjaan_m as pk2', 'pk2.id', '=', 'st.pekerjaanibufk')
                ->select(
                    'st.nis',
                    'st.nisn',
                    'st.namalengkap',
                    'st.tanggal_lahir',
                    'st.statussiswafk',
                    'kl.kelas',
                    'st.foto',
                    DB::raw("COALESCE(ab.status, 'Belum Tap') as status_tap"),
                    DB::raw("case when st.jeniskelaminfk = 1 then 'L' when st.jeniskelaminfk = 2 then 'P' else '-' end as jeniskelamin"),
                    'st.no_handphone',
                    'st.nfc_tag_id'
                )
                ->where('st.kelasfk', $request['idkelas'])
                ->whereNull('ab.objectsiswafk')
                ->where('st.kdprofile', 10)
                ->where('st.statusenabled', true)
                ->orderBy('st.nis', 'ASC')
                ->orderBy('st.namalengkap', 'ASC')
                ->get();

            $data = $data->map(function ($item) {
                $item->foto = !empty($item->foto) ? Cloudinary::getUrl($item->foto) : null;
                return $item;
            });

            if ($data) {
                return $this->successResponse('Get Data Siswa Berhasil', $data);
            } else {
                return $this->failedResponse('Gagal Get Data Siswa');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
    public function getDataSiswaByNis($nis)
    {
        try {
            $data = DB::table('students_m as st')
                ->join('kelas_m as kl', 'kl.id', '=', 'st.kelasfk')
                ->join('jeniskelamin_m as jk', 'jk.id', '=', 'st.jeniskelaminfk')
                ->join('agama_m as ag', 'ag.id', '=', 'st.agamafk')
                ->leftJoin('pekerjaan_m as pk1', 'pk1.id', '=', 'st.pekerjaanayahfk')
                ->leftJoin('pekerjaan_m as pk2', 'pk2.id', '=', 'st.pekerjaanibufk')
                ->leftjoin('statussiswa_m as ss', 'ss.id', '=', 'st.statussiswafk')
                ->leftJoin('provinsi_m as pm', 'pm.id', '=', 'st.provinsifk')
                ->select(
                    'st.nis',
                    'st.nisn',
                    'st.namalengkap',
                    'st.tanggal_lahir',
                    'st.tempat_lahir',
                    'kl.kelas',
                    'st.jeniskelaminfk',
                    'st.agamafk',
                    'st.kelasfk',
                    'st.statussiswafk',
                    DB::raw("case when st.jeniskelaminfk = 1 then 'L' when st.jeniskelaminfk = 2 then 'P' else '-' end as jeniskelamin"),
                    'ag.agama',
                    'st.alamat',
                    'pm.provinsi',
                    'st.provinsifk',
                    'st.kabkotafk',
                    'st.kecamatanfk',
                    'st.kelurahanfk',
                    'st.no_handphone',
                    'st.nama_ayah',
                    'st.nama_ibu',
                    'pk1.pekerjaan as pekerjaan_ayah',
                    'pk2.pekerjaan as pekerjaan_ibu',
                    'st.tahun_masuk',
                    'st.nfc_tag_id',
                    'st.foto',
                    'st.pekerjaanayahfk',
                    'st.pekerjaanibufk'
                )
                ->where('st.nis', $nis)
                ->where('st.kdprofile', 10)
                ->where('st.statusenabled', true);

            $data = $data->orderBy('st.namalengkap', 'ASC');
            $data = $data->first();

            if ($data) {
                $data->foto = !empty($data->foto)
                    ? Cloudinary::getUrl($data->foto)
                    : null;

                return $this->successResponse('Get Data Siswa Berhasil', $data);
            } else {
                return $this->failedResponse('Gagal Get Data Siswa');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
    public function postDataSiswa(Request $request)
    {

        $updatedAt = Carbon::now('Asia/Jakarta');

        try {
            $validatedData = $request->validate([
                'nis' => 'required|integer',
                'nisn' => 'nullable|integer',
                'namalengkap' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'tempat_lahir' => 'required|string|max:255',
                'kelasfk' => 'required|integer',
                'jeniskelaminfk' => 'required|integer',
                'agamafk' => 'nullable|integer',
                'alamat' => 'nullable|string|max:500',
                'provinsifk' => 'nullable|integer',
                'kabkotafk' => 'nullable|integer',
                'kecamatanfk' => 'nullable|integer',
                'kelurahanfk' => 'nullable|integer',
                'no_handphone' => 'nullable|string|max:15',
                'nama_ayah' => 'nullable|string|max:255',
                'nama_ibu' => 'nullable|string|max:255',
                'pekerjaan_ayah' => 'nullable|string|max:255',
                'pekerjaan_ibu' => 'nullable|string|max:255',
                'tahun_masuk' => 'nullable|string|max:255',
                'status_siswa' => 'nullable|string|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:800',

            ]);
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoPath = $foto->store('foto_siswa', 'public');
            } else {
                $fotoPath = null;
            }
            $studentId = DB::table('students_m')->insert([
                'nis' => $validatedData['nis'],
                'nisn' => $request['nisn'],
                'kdprofile' => 10,
                'statusenabled' => true,
                'namalengkap' => $request['namalengkap'],
                'tanggal_lahir' => $request['tanggal_lahir'],
                'tempat_lahir' => $request['tempat_lahir'],
                'kelasfk' => $request['kelasfk'],
                'jeniskelaminfk' => $request['jeniskelaminfk'],
                'agamafk' => $request['agamafk'],
                'alamat' => $request['alamat'],
                'provinsifk' => $request['provinsifk'],
                'kabkotafk' => $request['kabkotafk'],
                'kecamatanfk' => $request['kecamatanfk'],
                'kelurahanfk' => $request['kelurahanfk'],
                'no_handphone' => $request['no_handphone'],
                'nama_ayah' => $request['nama_ayah'],
                'nama_ibu' => $request['nama_ibu'],
                'pekerjaanayahfk' => $request['pekerjaan_ayah'],
                'pekerjaanibufk' => $request['pekerjaan_ibu'],
                'tahun_masuk' => $request['tahun_masuk'],
                'statussiswafk' => $request['status_siswa'],
                'foto' => $fotoPath,

                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);

            if ($studentId) {
                return $this->successResponse('Data Siswa berhasil disimpan', $studentId);
            } else {
                return $this->failedResponse('Gagal menyimpan data Siswa');
            }
        } catch (\Exception $e) {

            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function editDataSiswa(Request $request, $nis)
    {
        try {
            $student = DB::table('students_m')->where('nis', $nis)->first();
            if ($student == null) {
                return $this->resourceNotFoundResponse('Data siswa tidak ditemukan.');
            }

            // Inisialisasi fotoPublicId dengan foto lama
            $fotoPublicId = $student->foto ?? '';

            // Upload foto baru jika ada
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');

                $uploadResult = Cloudinary::upload($foto->getRealPath(), [
                    'folder' => 'foto_siswa'
                ]);

                $fotoPublicId = $uploadResult->getPublicId();
            }

            $updateData = [
                'nis' => $request['nis'],
                'nisn' => $request['nisn'],
                'kdprofile' => 10,
                'statusenabled' => true,
                'namalengkap' => $request['namalengkap'],
                'tanggal_lahir' => $request['tanggal_lahir'],
                'tempat_lahir' => $request['tempat_lahir'],
                'kelasfk' => $request['kelasfk'],
                'jeniskelaminfk' => $request['jeniskelaminfk'],
                'agamafk' => $request['agamafk'],
                'alamat' => $request['alamat'],
                'no_handphone' => $request['no_handphone'],
                'nama_ayah' => $request['nama_ayah'],
                'nama_ibu' => $request['nama_ibu'],
                'pekerjaanayahfk' => $request['pekerjaanayahfk'] ?? null,
                'pekerjaanibufk' => $request['pekerjaanibufk'] ?? null,
                'tahun_masuk' => $request['tahun_masuk'],
                'statussiswafk' => $request['statussiswafk'],
                'provinsifk' => $request['provinsifk'],
                'kabkotafk' => $request['kabkotafk'],
                'kecamatanfk' => $request['kecamatanfk'],
                'kelurahanfk' => $request['kelurahanfk'],
                'foto' => $fotoPublicId,
                'updated_at' => $this->now(),
            ];

            $updated = DB::table('students_m')
                ->where('nis', $nis)
                ->update($updateData);

            if ($updated) {
                // Cek field yang berubah
                $fieldLabels = [
                    'nis' => 'NIS',
                    'nisn' => 'NISN',
                    'namalengkap' => 'Nama Lengkap',
                    'tanggal_lahir' => 'Tanggal Lahir',
                    'tempat_lahir' => 'Tempat Lahir',
                    'kelasfk' => 'Kelas',
                    'jeniskelaminfk' => 'Jenis Kelamin',
                    'agamafk' => 'Agama',
                    'alamat' => 'Alamat',
                    'no_handphone' => 'No Handphone',
                    'nama_ayah' => 'Nama Ayah',
                    'nama_ibu' => 'Nama Ibu',
                    'pekerjaanayahfk' => 'Pekerjaan Ayah',
                    'pekerjaanibufk' => 'Pekerjaan Ibu',
                    'tahun_masuk' => 'Tahun Masuk',
                    'statussiswafk' => 'Status Siswa',
                    'provinsifk' => 'Provinsi',
                    'kabkotafk' => 'Kab/Kota',
                    'kecamatanfk' => 'Kecamatan',
                    'kelurahanfk' => 'Kelurahan',
                    'foto' => 'Foto',
                ];

                $changedFields = [];
                foreach ($updateData as $key => $newValue) {
                    if ($key === 'updated_at') {
                        continue; 
                    }

                    $oldValue = $student->$key ?? null;
                    $oldValueText = $oldValue === null || $oldValue === '' ? 'kosong' : $oldValue;
                    $newValueText = $newValue === null || $newValue === '' ? 'kosong' : $newValue;

                    if ($oldValueText != $newValueText) {
                        $changedFields[] = $fieldLabels[$key] ?? $key;
                    }
                }

                if (!empty($changedFields)) {
                    $logMessage = "Update data siswa '{$student->namalengkap}' (NIS: {$student->nis}), field yang diubah: " . implode(', ', $changedFields);
                    create_log(4, json_encode([$logMessage], JSON_UNESCAPED_UNICODE));
                }

                return $this->successResponse('Edit Data Siswa berhasil disimpan', $student);
            } else {
                return $this->failedResponse('Gagal menyimpan data Siswa');
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }


    public function deleteDataSiswa(Request $request)
    {
        try {
            $student = DB::table('students_m')->where('nis', $request['nis'])->first();

            if (!$student) {
                return $this->resourceNotFoundResponse('Data siswa tidak ditemukan.');
            }

            DB::table('students_m')->where('nis', $request['nis'])->update([
                'statusenabled' => false,
            ]);

            $logDeleteSiswa = [
                "Delete siswa '{$student->namalengkap}' dengan NIS: {$student->nis}"
            ];
            create_log(5, json_encode($logDeleteSiswa, JSON_UNESCAPED_UNICODE));

            return $this->successResponse('Hapus Siswa berhasil');
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function getDataDaftarInputSiswa(Request $request)
    {
        try {
            $data = DB::table('students_m as st')
                ->join('kelas_m as kl', 'kl.id', '=', 'st.kelasfk')
                ->join('jeniskelamin_m as jk', 'jk.id', '=', 'st.jeniskelaminfk')
                ->join('agama_m as ag', 'ag.id', '=', 'st.agamafk')
                ->leftJoin('pekerjaan_m as pk1', 'pk1.id', '=', 'st.pekerjaanayahfk')
                ->leftjoin('statussiswa_m as ss', 'ss.id', '=', 'st.statussiswafk')
                ->leftJoin('pekerjaan_m as pk2', 'pk2.id', '=', 'st.pekerjaanibufk')
                ->leftJoin('provinsi_m as pm', 'pm.id', '=', 'st.provinsifk')
                ->leftJoin('tabungan_siswa as ts', 'ts.student_id', '=', 'st.nis')
                ->select(
                    'st.nis',
                    'st.nisn',
                    'st.namalengkap',
                    'st.tanggal_lahir',
                    'st.tempat_lahir',
                    'kl.kelas',
                    'st.foto',
                    'ss.status_siswa',
                    'st.statussiswafk',
                    'st.kelasfk',
                    DB::raw("case when st.jeniskelaminfk = 1 then 'L' when st.jeniskelaminfk = 2 then 'P' else '-' end as jeniskelamin"),
                    'ag.agama',
                    'pm.provinsi',
                    'st.alamat',
                    'st.provinsifk',
                    'st.kabkotafk',
                    'st.kecamatanfk',
                    'st.kelurahanfk',
                    'st.no_handphone',
                    'st.nama_ayah',
                    'st.nama_ibu',
                    'pk1.pekerjaan as pekerjaan_ayah',
                    'pk2.pekerjaan as pekerjaan_ibu',
                    'st.tahun_masuk',
                    'st.nfc_tag_id',
                    'st.pekerjaanayahfk',
                    'st.pekerjaanibufk',
                    'ts.saldo'
                )
                ->where('st.kdprofile', 10)
                ->where('st.statusenabled', true)
                ->where(function ($query) use ($request) {
                    if (!empty($request['nis']) && !empty($request['nama']) && !empty($request['kelas'])) {

                        $query->whereRaw('CAST(st.nis AS TEXT) LIKE ?', ['%' . $request['nis'] . '%'])
                            ->whereRaw('LOWER(st.namalengkap) LIKE ?', ['%' . strtolower($request['nama']) . '%'])
                            ->whereIn('st.kelasfk', explode(',', $request['kelas']));
                    } else {
                        if (!empty($request['nis'])) {
                            $query->whereRaw('CAST(st.nis AS TEXT) LIKE ?', ['%' . $request['nis'] . '%']);
                        }
                        if (!empty($request['nama'])) {
                            $query->whereRaw('LOWER(st.namalengkap) LIKE ?', ['%' . strtolower($request['nama']) . '%']);
                        }
                        if (!empty($request['kelas'])) {
                            $query->whereIn('st.kelasfk', explode(',', $request['kelas']));
                        }
                    }
                });


            $data = $data->orderBy('st.namalengkap', 'ASC');
            $data = $data->get();

            $data = $data->map(function ($item) {
                $item->foto = !empty($item->foto) ? Cloudinary::getUrl($item->foto) : null;
                return $item;
            });

            if ($data) {
                return $this->successResponse('Get Data Siswa Berhasil', $data);
            } else {
                return $this->failedResponse('Gagal Get Data Siswa');
            }
        } catch (\Error $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function postNilaiSiswa(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'siswaId' => 'required|numeric|exists:students_m,nis',
                'mataPelajaran' => 'required|string|max:255',
                'tanggal' => 'required|date',
                'nilaiHarian' => 'required|numeric|min:0|max:100',
                'nilaiPR' => 'nullable|numeric|min:0|max:100',
                'nilaiTugas' => 'nullable|numeric|min:0|max:100',
                'ujian' => 'nullable|array',
                'ujian.nilaiUTS' => 'nullable|numeric|min:0|max:100',
                'ujian.nilaiUAS' => 'nullable|numeric|min:0|max:100',
                'catatanGuru' => 'nullable|string|max:500',
                'kelasId' => 'required|numeric',
                'guruId' => 'required|numeric',
                'tahunAjaranId' => 'nullable|numeric|exists:tahun_ajaran_m,id',
            ]);

            $mapelId = DB::table('mapel_m')
                ->where('nama_mapel', $validatedData['mataPelajaran'])
                ->value('id');

            if (!$mapelId) {
                return $this->failedResponse('Mata pelajaran tidak ditemukan');
            }

            $kelasId = $request->input('kelasId');
            $guruId = $request->input('guruId');
            $tahunAjaranId = $request->input('tahunAjaranId');

            if (empty($tahunAjaranId)) {
                $tahunAjaranId = DB::table('tahun_ajaran_m')
                    ->where('statusenabled', true)
                    ->orderByDesc('id')
                    ->value('id') ?? 1;
            }

            $nilaiSiswaId = DB::table('nilai_siswa')->insertGetId([
                'siswa_nis' => $validatedData['siswaId'],
                'mapel_id' => $mapelId,
                'kelas_id' => $kelasId,
                'tahun_ajaran_id' => $tahunAjaranId,
                'guru_id' => $guruId,
                'tanggal_penilaian' => $validatedData['tanggal'],
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);


            DB::table('detail_nilai_siswa')->insert([
                'nilai_siswa_id' => $nilaiSiswaId,
                'nilai_harian' => $validatedData['nilaiHarian'],
                'nilai_pr' => $validatedData['nilaiPR'],
                'nilai_tugas' => $validatedData['nilaiTugas'],
                'nilai_uts' => $validatedData['ujian']['nilaiUTS'] ?? null,
                'nilai_uas' => $validatedData['ujian']['nilaiUAS'] ?? null,
                'catatan_guru' => $validatedData['catatanGuru'],
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);

            $student = DB::table('students_m')->where('nis', $validatedData['siswaId'])->first();

            $fieldLabels = [
                'nilaiHarian'   => 'Nilai Harian',
                'nilaiPR'       => 'Nilai PR',
                'nilaiTugas'    => 'Nilai Tugas',
                'nilaiUTS'      => 'Nilai UTS',
                'nilaiUAS'      => 'Nilai UAS',
                'catatanGuru'   => 'Catatan',
            ];

            $changes = [];
            $changes[] = "Input Nilai siswa {$student->namalengkap} (NIS: {$student->nis})";
            $changes[] = "Mata Pelajaran: {$validatedData['mataPelajaran']}";

            if (!empty($validatedData['nilaiHarian'])) {
                $changes[] = "{$fieldLabels['nilaiHarian']}: {$validatedData['nilaiHarian']}";
            }
            if (!empty($validatedData['nilaiPR'])) {
                $changes[] = "{$fieldLabels['nilaiPR']}: {$validatedData['nilaiPR']}";
            }
            if (!empty($validatedData['nilaiTugas'])) {
                $changes[] = "{$fieldLabels['nilaiTugas']}: {$validatedData['nilaiTugas']}";
            }
            if (!empty($validatedData['ujian']['nilaiUTS'] ?? null)) {
                $changes[] = "{$fieldLabels['nilaiUTS']}: {$validatedData['ujian']['nilaiUTS']}";
            }
            if (!empty($validatedData['ujian']['nilaiUAS'] ?? null)) {
                $changes[] = "{$fieldLabels['nilaiUAS']}: {$validatedData['ujian']['nilaiUAS']}";
            }
            if (!empty($validatedData['catatanGuru'])) {
                $changes[] = "{$fieldLabels['catatanGuru']}: {$validatedData['catatanGuru']}";
            }

            if (!empty($changes)) {
                create_log(3, json_encode($changes, JSON_UNESCAPED_UNICODE));
            }

            return $this->successResponse('Nilai siswa berhasil disimpan');
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
    public function postTabunganSiswa(Request $request)
    {
        // Validasi input dari FE
        $validated = $request->validate([
            'siswa_id'           => 'required|integer|exists:students_m,nis',
            'jenis_transaksi_id' => 'required|integer|exists:jenis_transaksi_tabungan,id',
            'jumlah'             => 'required|numeric|min:1',
            'tanggal'            => 'required|date',
            'keterangan'         => 'nullable|string|max:255',
            'guru_id'            => 'required|integer|exists:pegawai_m,id',
        ]);

        $studentId  = $validated['siswa_id'];
        $pegawaiId  = $validated['guru_id'];
        $tanggal    = $validated['tanggal'];
        $jumlah     = $validated['jumlah'];
        $jenisId    = $validated['jenis_transaksi_id'];
        $keterangan = $validated['keterangan'] ?? null;

        DB::beginTransaction();
        try {
            $kdprofile = 10;
            $tabungan = DB::table('tabungan_siswa')
                ->where('student_id', $studentId)
                ->first();

            if (!$tabungan) {
                $tabungan_id = DB::table('tabungan_siswa')->insertGetId([
                    'kdprofile'  => $kdprofile,
                    'student_id' => $studentId,
                    'saldo'      => 0,
                    'created_at' => $this->now(),
                    'updated_at' => $this->now(),
                ]);
            } else {
                $tabungan_id = $tabungan->id;
            }

            $transaksi_id = DB::table('transaksi_tabungan')->insertGetId([
                'kdprofile'          => $kdprofile,
                'tabungan_id'        => $tabungan_id,
                'pegawai_id'         => $pegawaiId,
                'jenis_transaksi_id' => $jenisId,
                'jumlah'             => $jumlah,
                'keterangan'         => $keterangan,
                'tanggal_transaksi'  => $this->now(),
                'created_at'         => $this->now(),
                'updated_at'         => $this->now(),
            ]);

            $currentSaldo = $tabungan ? $tabungan->saldo : 0;

            if ($jenisId == 1) {
                $newSaldo = $currentSaldo + $jumlah;
            } elseif ($jenisId == 2) {
                if ($currentSaldo < $jumlah) {
                    throw new \Exception('Saldo tidak cukup untuk penarikan');
                }
                $newSaldo = $currentSaldo - $jumlah;
            } else {
                throw new \Exception('Jenis transaksi tidak valid');
            }

            DB::table('tabungan_siswa')
                ->where('id', $tabungan_id)
                ->update([
                    'saldo'      => $newSaldo,
                    'updated_at' => $this->now(),
                ]);

            DB::commit();

            $student = DB::table('students_m')->where('nis', $studentId)->first();
            $jenisNama = $jenisId == 1 ? 'menabung' : 'penarikan';
            $logKeterangan = [
                "Tabungan {$student->namalengkap} (NIS: {$student->nis})",
                "Jenis Transaksi: {$jenisNama}",
                "Nominal: {$jumlah}",
                "Keterangan: " . ($keterangan ?? '-')
            ];
            create_log(3, json_encode($logKeterangan, JSON_UNESCAPED_UNICODE));

            NotificationController::buatNotifikasiTabungan($student, $pegawaiId, $jenisId, $jumlah);

            return response()->json([
                'status'  => true,
                'message' => 'Tabungan berhasil disimpan',
                'data'    => [
                    'tabungan_id'  => $tabungan_id,
                    'transaksi_id' => $transaksi_id,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan tabungan',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function getTabunganSiswa($nis)
    {
        try {
            $siswa = DB::table('students_m')->where('nis', $nis)->first();
            if (!$siswa) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            $riwayat = DB::table('transaksi_tabungan as tt')
                ->join('tabungan_siswa as ts', 'ts.id', '=', 'tt.tabungan_id')
                ->join('jenis_transaksi_tabungan as jt', 'jt.id', '=', 'tt.jenis_transaksi_id')
                ->leftJoin('pegawai_m as pg', 'pg.id', '=', 'tt.pegawai_id')
                ->where('ts.student_id', $nis)
                ->select(
                    'tt.id',
                    'jt.nama as jenis_transaksi',
                    'tt.jumlah',
                    'tt.keterangan',
                    'tt.tanggal_transaksi',
                    'pg.namalengkap as nama_guru'
                )
                ->orderBy('tt.tanggal_transaksi', 'desc')
                ->get();

            $saldo = DB::table('tabungan_siswa')
                ->where('student_id', $nis)
                ->value('saldo');

            return response()->json([
                'status'  => true,
                'message' => 'Data tabungan siswa',
                'data'    => [
                    'siswa'   => [
                        'nis'   => $siswa->nis,
                        'nama'  => $siswa->namalengkap,
                    ],
                    'saldo'   => $saldo ?? 0,
                    'riwayat' => $riwayat
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil data tabungan',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
