<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App;
use App\Events\MessageSentPublicChannel;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AbsenController extends Controller
{
    use  App\Traits\ApiResponse\ApiResponse;
    use App\Traits\LogingSystems\LogingSystems;
    protected function now()
    {
        return Carbon::now('Asia/Jakarta');
    }
    public function postAbsensiSiswa(Request $request)
    {
        $student = DB::table('students_m')->where('nfc_tag_id', $request['nfc'])->first();
        if (!$student) {
            return $this->resourceNotFoundResponse('Data siswa tidak ditemukan.');
        }

        try {
            $waktuSekarang = Carbon::now('Asia/Jakarta');

            $waktuBatasMasuk = Carbon::createFromTime(7, 15, 0, 'Asia/Jakarta'); // 07:15
            $waktuBatasPulang = Carbon::createFromTime(15, 0, 0, 'Asia/Jakarta'); // 15:00

            $status = $waktuSekarang->lessThanOrEqualTo($waktuBatasMasuk) ? 'Tepat waktu' : 'Terlambat';

            $absensiHariIni = DB::table('absensi_m')
                ->where('objectsiswafk', $student->nis)
                ->whereDate('waktu_tap_in', $waktuSekarang->toDateString())
                ->first();

            if (!$absensiHariIni) {

                $inserted = DB::table('absensi_m')->insert([
                    'objectsiswafk' => $student->nis,
                    'waktu_tap_in' => $waktuSekarang,
                    'kdprofile' => 10,
                    'statusenabled' => true,
                    'status' => $status,
                    'status_tap' => true,
                    'keterangan' => null,
                    'waktu_update' => $waktuSekarang
                ]);

                if ($inserted) {
                    $notifTitle = 'Absensi ' . $student->namalengkap;
                    $notifDetail = 'Siswa ' . $student->namalengkap . ' melakukan tap masuk pada ' . $this->now();
                    $notifCreatedAt = $waktuSekarang->format('Y-m-d H:i:s');
                    $this->buatNotifikasiAbsen($student, 'masuk', $notifTitle, $notifDetail, $notifCreatedAt);
                    MessageSentPublicChannel::dispatch($notifTitle, $notifDetail, $notifCreatedAt);

                    $logKeterangan = [
                        "Absensi siswa {$student->namalengkap} (NIS: {$student->nis}) dengan status 'Tap In'"
                    ];
                    create_log(3, json_encode($logKeterangan, JSON_UNESCAPED_UNICODE));

                    return $this->successCreatedResponse('Berhasil Tap In', [
                        'data' => true,
                        'nama_siswa' => $student->namalengkap
                    ]);
                }
            } else {
                // Tap Out
                if ($absensiHariIni->waktu_tap_out === null) {
                    $updated = DB::table('absensi_m')
                        ->where('id', $absensiHariIni->id)
                        ->update([
                            'waktu_tap_out' => $waktuSekarang,
                            'waktu_update' => $waktuSekarang,
                            // // Bisa tambahin status pulang
                            // 'status_pulang' => $waktuSekarang->lessThan($waktuBatasPulang) ? 'Pulang Cepat' : 'Pulang'
                        ]);

                    if ($updated) {
                        $notifTitle = 'Absensi ' . $student->namalengkap;
                        $notifDetail = 'Siswa ' . $student->namalengkap . ' melakukan tap pulang pada ' . $this->now();
                        $notifCreatedAt = $waktuSekarang->format('Y-m-d H:i:s');
                        $this->buatNotifikasiAbsen($student, 'pulang', $notifTitle, $notifDetail, $notifCreatedAt);
                        MessageSentPublicChannel::dispatch($notifTitle, $notifDetail, $notifCreatedAt);

                        // LOG
                        $logKeterangan = [
                            "Absensi siswa {$student->namalengkap} (NIS: {$student->nis}) dengan status 'Tap Out'"
                        ];
                        create_log(3, json_encode($logKeterangan, JSON_UNESCAPED_UNICODE));

                        return $this->successCreatedResponse('Berhasil Tap Out', [
                            'data' => true,
                            'nama_siswa' => $student->namalengkap
                        ]);
                    }
                } else {
                    return $this->failedResponse('Absensi untuk ' . $student->namalengkap . ' sudah lengkap hari ini.');
                }
            }

            return $this->failedResponse('Gagal mencatat absensi.');
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
    public function getAbsensiSiswa(Request $request)
    {
        try {
            $absensi = DB::table('absensi_m as ab')
                ->leftJoin('students_m as st', 'st.nis', '=', 'ab.objectsiswafk')
                ->join('kelas_m as kl', 'kl.id', '=', 'st.kelasfk')
                ->select(
                    'ab.id as absensi_id',
                    'st.nis',
                    'st.nisn',
                    'st.namalengkap',
                    'ab.status',
                    'ab.keterangan',
                    'kl.kelas',
                    'st.foto',
                    'ab.filebukti',
                    DB::raw("CASE WHEN ab.waktu_tap_in IS NULL THEN '-' ELSE TO_CHAR(ab.waktu_tap_in, 'YYYY-MM-DD HH24:MI:SS') END AS waktu_tap_in"),
                    DB::raw("CASE WHEN ab.waktu_tap_out IS NULL THEN '-' ELSE TO_CHAR(ab.waktu_tap_out, 'YYYY-MM-DD HH24:MI:SS') END AS waktu_tap_out"),
                    DB::raw("case when st.jeniskelaminfk = 1 then 'L' when st.jeniskelaminfk = 2 then 'P' else '-' end as jeniskelamin"),
                )
                ->where('st.kdprofile', 10)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('ab.waktu_tap_in', [$request['tglAwal'], $request['tglAkhir']])
                        ->orWhereNull('ab.waktu_tap_in');
                });


            if (isset($request['idkelas']) && $request['idkelas'] != "" && $request['idkelas'] != "undefined") {
                $absensi = $absensi->where('st.kelasfk', $request['idkelas']);
            }
            $absensi = $absensi->where('st.statusenabled', true)
                ->orderBy('ab.waktu_tap_in', 'ASC')
                ->get();

            if ($absensi->isEmpty()) {
                return $this->resourceNotFoundResponse('Data absensi tidak ditemukan.');
            }

            $absensi = $absensi->map(function ($item) {
                $item->foto = !empty($item->foto)
                    ? Cloudinary::getUrl($item->foto)
                    : null;

                return $item;
            });

            return $this->successCreatedResponse('Fetched data absensi successfull', $absensi);
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function postStatusAbsensiSiswa(Request $request)
    {
        $waktuSekarang = Carbon::now('Asia/Jakarta');

        $student = DB::table('students_m')->where('nis', $request['nis'])->first();
        if (!$student) {
            return $this->resourceNotFoundResponse('Data siswa tidak ditemukan.');
        }
        try {
            $filePaths = [];
            $insertData = [];
            $hari = 0;
            $tanggal = Carbon::now('Asia/Jakarta');

            $validated = $request->validate([
                'status' => 'required|string',
                'keterangan' => 'nullable|string',
                'jumlah_hari' => 'required|integer|min:1',
                'bukti' => 'nullable|array',
                'bukti.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
            ]);


            $hasBuktiFile = false;
            foreach ($request->allFiles() as $key => $file) {
                if (str_starts_with($key, 'bukti_')) {
                    $hasBuktiFile = true;
                    break;
                }
            }

            $filePaths = [];

            if ($hasBuktiFile) {
                foreach ($request->allFiles() as $key => $file) {
                    if (str_starts_with($key, 'bukti_')) {

                        $uploadResult = Cloudinary::uploadFile($file->getRealPath(), [
                            'folder' => 'bukti_absensi',
                        ]);


                        $filePaths[] = [
                            'url' => $uploadResult->getSecurePath(),
                            'public_id' => $uploadResult->getPublicId(),
                        ];
                    }
                }
            } else {

                $filePaths = $existingData->file_paths ?? [];
            }

            $statusbaru = $validated['status'];
            $keterangan = $validated['keterangan'];

            $insertedDates = [];
            $skippedDates = [];

            while ($hari < $validated['jumlah_hari']) {
                if (!$tanggal->isWeekend()) {
                    $sudahAda = DB::table('absensi_m')
                        ->where('objectsiswafk', $student->nis)
                        ->whereDate('waktu_tap_in', $tanggal->toDateString())
                        ->exists();

                    if (!$sudahAda) {
                        $insertData[] = [
                            'objectsiswafk' => $student->nis,
                            'waktu_tap_in' => $tanggal->copy(),
                            'kdprofile' => 10,
                            'statusenabled' => true,
                            'status' => $statusbaru,
                            'status_tap' => true,
                            'filebukti' => json_encode($filePaths),
                            'keterangan' => $keterangan,
                            'waktu_update' => Carbon::now('Asia/Jakarta')
                        ];
                        $insertedDates[] = $tanggal->toDateString();
                    } else {
                        $skippedDates[] = $tanggal->toDateString();
                    }
                    $hari++;
                }
                $tanggal->addDay();
            }

            $absensiStatus = DB::table('absensi_m')->insert($insertData);

            if ($insertData) {

                // LOG Absensi
                $logKeterangan = [
                    "Absensi siswa {$student->namalengkap} (NIS: {$student->nis}) dengan status '{$statusbaru}'"
                ];
                create_log(3, json_encode($logKeterangan, JSON_UNESCAPED_UNICODE));

                // NOTIFIKASI
                $notifTitle = 'Absensi ' . $student->namalengkap;
                $notifDetail = 'Siswa ' . $student->namalengkap . ' melakukan tap pulang pada ' . $waktuSekarang->format('H:i d/m/Y');
                $notifCreatedAt = $waktuSekarang->format('Y-m-d H:i:s');
                MessageSentPublicChannel::dispatch($notifTitle, $notifDetail, $notifCreatedAt);

                return $this->successCreatedResponse('Berhasil Melakukan Update Status', [
                    'data' => true,
                    'status' => $statusbaru,
                ]);
            } else {
                return $this->failedResponse("Tidak ada absensi baru yang ditambahkan. Tanggal berikut sudah ada: " . implode(', ', $skippedDates));
            }
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function editAbsensiSiswa(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
            'waktu_tap_in' => 'nullable|date',
            'waktu_tap_out' => 'nullable|date',
            'bukti' => 'nullable|array',
            'bukti.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $absensi = DB::table('absensi_m')->where('id', $validated['id'])->first();
        if (!$absensi) {
            return $this->resourceNotFoundResponse('Data absensi tidak ditemukan.');
        }

        $filePaths = [];
        $hasBuktiFile = false;
        foreach ($request->allFiles() as $key => $file) {
            if (str_starts_with($key, 'bukti_')) {
                $hasBuktiFile = true;
                break;
            }
        }

        if ($hasBuktiFile) {
            foreach ($request->allFiles() as $key => $file) {
                if (str_starts_with($key, 'bukti_')) {
                    $uploadResult = Cloudinary::uploadFile($file->getRealPath(), [
                        'folder' => 'bukti_absensi',
                    ]);

                    $filePaths[] = [
                        'url' => $uploadResult->getSecurePath(),
                        'public_id' => $uploadResult->getPublicId(),
                    ];
                }
            }
        } else {
            $existingFiles = $absensi->filebukti ? json_decode($absensi->filebukti, true) : [];
            $filePaths = is_array($existingFiles) ? $existingFiles : [];
        }

        $statusBaru = $validated['status'];
        $updateData = [
            'status' => $statusBaru,
            'keterangan' => $validated['keterangan'] ?? null,
            'filebukti' => empty($filePaths) ? null : json_encode($filePaths),
            'waktu_update' => Carbon::now('Asia/Jakarta')
        ];

        if (in_array($statusBaru, ['Sakit', 'Izin', 'Alpa'])) {
            $updateData['waktu_tap_out'] = null;
        } else {
            if (array_key_exists('waktu_tap_in', $validated) && $validated['waktu_tap_in']) {
                $updateData['waktu_tap_in'] = Carbon::parse($validated['waktu_tap_in']);
            }
            if (array_key_exists('waktu_tap_out', $validated)) {
                $updateData['waktu_tap_out'] = $validated['waktu_tap_out']
                    ? Carbon::parse($validated['waktu_tap_out'])
                    : null;
            }
        }

        DB::table('absensi_m')->where('id', $validated['id'])->update($updateData);

        $student = DB::table('students_m')->where('nis', $absensi->objectsiswafk)->first();
        if ($student) {
            $logKeterangan = [
                "Edit absensi siswa {$student->namalengkap} (NIS: {$student->nis}) dengan status '{$statusBaru}'"
            ];
            create_log(3, json_encode($logKeterangan, JSON_UNESCAPED_UNICODE));
        }

        return $this->successCreatedResponse('Berhasil memperbarui absensi', [
            'data' => true,
            'status' => $statusBaru
        ]);
    }

    public function postJadwalAbsensi(Request $request)
    {
        $startDate = Carbon::parse($request['tanggal_mulai']);
        $endDate = Carbon::parse($request['tanggal_akhir']);
        $tglAyeuna = date('Y-m-d H:i:s');

        $daysOfWeek = [];
        $dayMapping = [
            'SENIN' => 'Monday',
            'SELASA' => 'Tuesday',
            'RABU' => 'Wednesday',
            'KAMIS' => 'Thursday',
            'JUMAT' => 'Friday',
            'SABTU' => 'Saturday',
            'MINGGU' => 'Sunday',
        ];


        $hariString = implode(', ', $request['hari']);
        $requestedDays = explode(', ', $hariString);
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $dayName = $date->format('l');
            $daysOfWeek[$formattedDate] = $dayName;
        }

        $filteredDates = [];
        foreach ($daysOfWeek as $date => $day) {

            $mappedRequestedDays = array_map(function ($hari) use ($dayMapping) {
                return $dayMapping[$hari];
            }, $requestedDays);

            if (in_array($day, $mappedRequestedDays)) {
                $filteredDates[] = $date;
            }
        }
        try {
            $allInserted = true;
            $insertedIds = [];
            foreach ($filteredDates as $date) {
                $randomId = mt_rand(10001, PHP_INT_MAX);
                $studentId = DB::table('mapjadwaltoguru_t')->insert([
                    'id' => $randomId,
                    'statusenabled' => true,
                    'kdprofile' => 10,
                    'objectgurufk' => $request['objectgurufk'],
                    'tanggal_belajar' => $date,
                    'tanggal_input' => $tglAyeuna,
                    'jam_mulai' => $request['jam_mulai'],
                    'jam_akhir' => $request['jam_akhir'],
                    'keterangan' => $request['keterangan'],
                ]);

                if ($studentId) {
                    $insertedIds[] = $studentId;
                } else {
                    $allInserted = false;
                }
            }

            if ($allInserted) {
                return $this->successResponse('Setting jadwal berhasil disimpan', $insertedIds);
            } else {
                return $this->failedResponse('Gagal menyimpan data setting jadwal');
            }
        } catch (\Exception $e) {

            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }

    public function getAbsensiSiswaByNis(Request $request)
    {
        $validated = $request->validate([
            'nis'    => 'required|string',
            'range'  => 'nullable|string|in:minggu,bulan,semester',
            'status' => 'nullable|string|in:hadir,sakit,izin,alpa',
        ]);

        $nis    = $validated['nis'];
        $range  = $validated['range'] ?? 'minggu';
        $status = $validated['status'] ?? null;

        // Tentukan rentang tanggal
        $startDate = match ($range) {
            'bulan'    => now()->subDays(30),
            'semester' => now()->subMonths(6),
            default    => now()->subDays(7),
        };

        // Query absensi
        $query = DB::table('absensi_m as ab')
            ->leftJoin('students_m as st', 'st.nis', '=', 'ab.objectsiswafk')
            ->join('kelas_m as kl', 'kl.id', '=', 'st.kelasfk')
            ->select(
                'st.nis',
                'st.nisn',
                'st.namalengkap',
                'ab.status',
                'ab.keterangan',
                'kl.kelas',
                'st.foto',
                'ab.filebukti',
                'ab.status_tap',
                DB::raw("COALESCE(TO_CHAR(ab.waktu_tap_in, 'YYYY-MM-DD HH24:MI:SS'), '-') AS waktu_tap"),
                DB::raw("CASE st.jeniskelaminfk WHEN 1 THEN 'L' WHEN 2 THEN 'P' ELSE '-' END AS jeniskelamin")
            )
            ->where([
                ['st.kdprofile', '=', 10],
                ['st.statusenabled', '=', true],
                ['st.nis', '=', $nis],
            ])
            ->whereDate('ab.waktu_tap_in', '>=', $startDate);

        if ($status) {
            $query->where('ab.status', $status);
        }

        $absensi = $query->orderBy('ab.waktu_tap_in')->get();

        if ($absensi->isEmpty()) {
            return $this->resourceNotFoundResponse('Data absensi tidak ditemukan.');
        }

        return $this->successCreatedResponse('Fetched data absensi successfully.', $absensi);
    }
    private function buatNotifikasiAbsen($student, $status)
    {

        $waliKelas = DB::table('pegawai_m')
            ->where('kelasfk', $student->kelasfk)
            ->where('is_wali_kelas', true)
            ->where('statusenabled', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$waliKelas) {
            return;
        }

        $notifId = DB::table('notifikasi_m')->insertGetId([
            'statusenabled' => true,
            'kdprofile' => '10',
            'notif_type' => 'absensi',
            'notif_title' => 'Absensi ' . $student->namalengkap,
            'notif_detail' => $student->namalengkap . ' melakukan tap ' . $status . ' pada ' . now()->format('H:i d/m/Y'),
            'is_forall' => false,
            'created_at' => $this->now(),
            'updated_at' => $this->now(),
        ]);

        DB::table('notifikasi_pegawai')->insert([
            'notif_id' => $notifId,
            'pegawai_id' => $waliKelas->id,
            'is_read' => false,
            'created_at' => $this->now(),
            'updated_at' => $this->now(),
        ]);
    }
    public function getChartAbsensi(Request $request)
    {
        try {
            $validated = $request->validate([
                'nis'    => 'required|string',
            ]);

            $nis    = $validated['nis'];

            $absensi = DB::table('absensi_m as ab')
                ->leftJoin('students_m as st', 'st.nis', '=', 'ab.objectsiswafk')
                ->select(
                    DB::raw("TO_CHAR(ab.waktu_tap_in, 'Mon') as bulan"),
                    'ab.status',
                    DB::raw("COUNT(*) as total")
                )
                ->where([
                    ['st.kdprofile', '=', 10],
                    ['st.statusenabled', '=', true],
                    ['st.nis', '=', $nis],
                ])
                ->groupBy(DB::raw("TO_CHAR(ab.waktu_tap_in, 'Mon')"), 'ab.status')
                ->orderBy(DB::raw("MIN(ab.waktu_tap_in)"))
                ->get();


            if ($absensi->isEmpty()) {
                return $this->resourceNotFoundResponse('Data absensi tidak ditemukan.');
            }

            return $this->successCreatedResponse('Fetched data absensi successfully.', $absensi);
        } catch (\Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage());
        }
    }
}
