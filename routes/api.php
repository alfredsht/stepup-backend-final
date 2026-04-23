<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Broadcast::routes();
Route::get('/cek-koneksi', function () {
    return response()->json(['status' => 'Laravel Connect!']);
});
Route::middleware('throttle:public')->group(function () {
    $prefix = "services/stepup2025";
    Route::get("$prefix/public-key", function () {
        $publicKey = Storage::disk('local')->get('keys/public.pem');
        return response()->json(['publicKey' => $publicKey]);
    });
    Route::middleware('encrypt.response')->post("$prefix/login", [App\Http\Controllers\Auth\AuthController::class, "login"]);
    Route::post("$prefix/logout", [App\Http\Controllers\Auth\AuthController::class, "logout"]);
    // Route::post("$prefix/otp-check/{userName}", [App\Http\Controllers\Auth\AuthController::class, "CekUsernameOtp"]);
    Route::post("$prefix/auth/send-otp", [App\Http\Controllers\Auth\AuthController::class, "sendOtp"]);
    Route::post("$prefix/create-otp", [App\Http\Controllers\Auth\AuthController::class, "createOtpRequest"]);
    Route::get("$prefix/verify-otp", [App\Http\Controllers\Auth\AuthController::class, "verifyOtp"]);
    Route::post("$prefix/resend-otp", [App\Http\Controllers\Auth\AuthController::class, "resendOtp"]);
    Route::post("$prefix/pegawai/new-pass", [App\Http\Controllers\Auth\AuthController::class, "updatePassword"]);
    Route::post("$prefix/absensi/absensi-siswa", [App\Http\Controllers\Absensi\AbsenController::class, "postAbsensiSiswa"]);
    Route::post("$prefix/absensi/status-absensi-siswa", [App\Http\Controllers\Absensi\AbsenController::class, "postStatusAbsensiSiswa"]);
    Route::get("$prefix/absensi/absensi-siswa", [App\Http\Controllers\Absensi\AbsenController::class, "getAbsensiSiswa"]);
    Route::post("$prefix/absensi/edit-absensi-siswa", [App\Http\Controllers\Absensi\AbsenController::class, "editAbsensiSiswa"]);
    Route::get("$prefix/registrasi/data-rekap-tap", [App\Http\Controllers\Master\MasterController::class, "getDataRekapTap"]);
    Route::get("$prefix/registrasi/data-rekap-card", [App\Http\Controllers\Master\MasterController::class, "getDataRekapCard"]);
    Route::get("$prefix/registrasi/test", [App\Http\Controllers\Master\MasterController::class, "test"]);
    Route::middleware(['auth.jwt', 'throttle:authenticated'])->group(function () use ($prefix) {
        Route::middleware('encrypt.response')->get("$prefix/siswa", [App\Http\Controllers\Siswa\SiswaController::class, "getDataSiswa"]);
        Route::get("$prefix/siswa-bykelas", [App\Http\Controllers\Siswa\SiswaController::class, "getDataSiswaAbsensiByKelas"]);
        Route::get("$prefix/siswa/{nis}", [App\Http\Controllers\Siswa\SiswaController::class, "getDataSiswaByNis"]);
        Route::post("$prefix/registrasi/siswa/add", [App\Http\Controllers\Siswa\SiswaController::class, "postDataSiswa"]);
        Route::post("$prefix/registrasi/siswa/{nis}", [App\Http\Controllers\Siswa\SiswaController::class, "editDataSiswa"]);
        Route::post("$prefix/registrasi/siswa", [App\Http\Controllers\Siswa\SiswaController::class, "deleteDataSiswa"]);
        Route::get("$prefix/registrasi/data-combo", [App\Http\Controllers\Master\MasterController::class, "getDataCombo"]);
        Route::get("$prefix/registrasi/data-combo-prov", [App\Http\Controllers\Master\MasterController::class, "getDataComboProv"]);
        Route::get("$prefix/registrasi/data-combo-kabupaten/{provinsi_id}", [App\Http\Controllers\Master\MasterController::class, "getDataComboKabupaten"]);
        Route::get("$prefix/registrasi/data-combo-kecamatan/{kabupaten_id}", [App\Http\Controllers\Master\MasterController::class, "getDataComboKecamatan"]);
        Route::get("$prefix/registrasi/data-combo-kelurahan/{kecamatan_id}", [App\Http\Controllers\Master\MasterController::class, "getDataComboKelurahan"]);
        Route::get("$prefix/master/data-combo-pegawai", [App\Http\Controllers\Master\MasterController::class, "getDataComboPegawai"]);
        Route::post("$prefix/absensi/jadwal-absensi", [App\Http\Controllers\Absensi\AbsenController::class, "postJadwalAbsensi"]);
        Route::get("$prefix/daftar-input-siswa", [App\Http\Controllers\Siswa\SiswaController::class, "getDataDaftarInputSiswa"]);
        Route::get("$prefix/master/data-mapel", [App\Http\Controllers\Master\MasterController::class, "getMapel"]);
        Route::get("$prefix/absensi/rekap-bynis", [App\Http\Controllers\Absensi\AbsenController::class, "getAbsensiSiswaByNis"]);
        Route::post("$prefix/master/mata-pelajaran", [App\Http\Controllers\Master\MasterController::class, "postMataPelajaran"]);
        Route::put("$prefix/master/mata-pelajaran/{id}", [App\Http\Controllers\Master\MasterController::class, "updateMataPelajaran"]);
        Route::get("$prefix/master/data-mapel-chart", [App\Http\Controllers\Master\MasterController::class, "getDataMapelChart"]);
        Route::get("$prefix/master/data-pegawai", [App\Http\Controllers\Master\MasterController::class, "getDataPegawai"]);
        Route::post("$prefix/master/edit-data-pegawai/{id}", [App\Http\Controllers\Master\MasterController::class, "updateDataPegawai"]);
        Route::post("$prefix/master/add-data-pegawai", [App\Http\Controllers\Master\MasterController::class, "postDataPegawai"]);
        Route::post("$prefix/siswa/submit-nilai", [App\Http\Controllers\Siswa\SiswaController::class, "postNilaiSiswa"]);
        Route::get("$prefix/notifikasi", [App\Http\Controllers\Master\NotificationController::class, "getNotifications"]);
        Route::get("$prefix/master/data-pegawai-id/{PegawaiID}", [App\Http\Controllers\Master\MasterController::class, "getDataPegawaiById"]);
        Route::post("$prefix/pegawai/change-password", [App\Http\Controllers\Auth\AuthController::class, "changeUserPassword"]);
        Route::get("$prefix/master/data-combo-jenistransaksi", [App\Http\Controllers\Master\MasterController::class, "getDataComboJenisTransaksi"]);
        Route::post("$prefix/siswa/submit-tabungan", [App\Http\Controllers\Siswa\SiswaController::class, "postTabunganSiswa"]);
        Route::get("$prefix/siswa/tabungan-siswa/{nis}", [App\Http\Controllers\Siswa\SiswaController::class, "getTabunganSiswa"]);
        Route::get("$prefix/master/ranking", [App\Http\Controllers\Master\MasterController::class, "getRankingSiswa"]);
        Route::get("$prefix/absensi/chart-absensi", [App\Http\Controllers\Absensi\AbsenController::class, "getChartAbsensi"]);
        Route::get("$prefix/sysadmin/log-data", [App\Http\Controllers\Log\LogController::class, "getLogData"]);
        Route::get("$prefix/menus", [App\Http\Controllers\Admin\MenuController::class, "indexMenus"]);
        Route::get("$prefix/menus/{PegawaiID}", [App\Http\Controllers\Admin\MenuController::class, "getMenusByUser"]);
        Route::get("$prefix/master/data-combo-jenispegawai", [App\Http\Controllers\Master\MasterController::class, "getDataComboJenisPegawai"]);
        Route::post("$prefix/sysadmin/set-mapping-menu", [App\Http\Controllers\Admin\MenuController::class, "setMappingMenu"]);
        Route::get("$prefix/sysadmin/get-mapping-menu", [App\Http\Controllers\Admin\MenuController::class, "getMappingMenu"]);
        Route::post("$prefix/sysadmin/delete-mapping-menu", [App\Http\Controllers\Admin\MenuController::class, "deleteMenuMapping"]);
        Route::post("$prefix/sysadmin/update-menu", [App\Http\Controllers\Admin\MenuController::class, "updateMenu"]);
        Route::get("$prefix/sysadmin/get-all-mapping-menu", [App\Http\Controllers\Admin\MenuController::class, "getAllMappings"]);
        Route::post("$prefix/notifikasi/mark-read", [App\Http\Controllers\Master\NotificationController::class, "markAsRead"]);
        Route::post("$prefix/notifikasi/delete", [App\Http\Controllers\Master\NotificationController::class, "deleteNotifications"]);
        Route::post("$prefix/pegawai/user-setting", [\App\Http\Controllers\User\UserController::class, "updateUserSetting"]);
        Route::get("$prefix/master/tahun-ajaran", [App\Http\Controllers\Rapot\RapotController::class, "getTahunAjaran"]);
        Route::post("$prefix/pegawai/profile/update", [\App\Http\Controllers\User\UserController::class, "updateOwnProfile"]);
        Route::post("$prefix/pegawai/profile/update-photo", [\App\Http\Controllers\User\UserController::class, "updateOwnProfilePhoto"]);
        Route::get("$prefix/rapot/preview/{nis}", [App\Http\Controllers\Rapot\RapotController::class, "previewRapot"]);
        Route::post("$prefix/rapot/wali-kelas/{nis}", [App\Http\Controllers\Rapot\RapotController::class, "updateWaliKelasRapot"]);
    });
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
