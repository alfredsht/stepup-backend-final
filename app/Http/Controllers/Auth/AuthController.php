<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Master\LoginUser;
use App\Models\Master\Pegawai;
use App\Models\Otp\OtpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'createOtpRequest', 'verifyOtp', 'CekUsernameOtp', 'resendOtp', 'updatePassword', 'sendOtp']]);
    }
    public function login(Request $request)
    {
        // $privateKeyPem = Storage::disk('local')->get('keys/private.pem');
        // $privateKey = openssl_pkey_get_private($privateKeyPem);

        // $encryptedPayload = base64_decode($request->input('payload'));
        // if (!openssl_private_decrypt($encryptedPayload, $decrypted, $privateKey)) {
        //     return response()->json(['error' => 'Gagal mendekripsi data'], 400);
        // }

        // $credentials = json_decode($decrypted, true);
        // if (!$credentials || !isset($credentials['namauser'], $credentials['katasandi'])) {
        //     return response()->json(['error' => 'Format data tidak valid'], 400);
        // }

        $credentials = $request->validate([
            'namauser' => 'required|string',
            'katasandi' => 'required|string',
        ]);

        $identifier = trim($credentials['namauser']);
        $phoneDigits = preg_replace('/\D+/', '', $identifier);
        $phoneCandidates = [];

        if (!empty($phoneDigits)) {
            $phoneCandidates[] = $phoneDigits;

            if (str_starts_with($phoneDigits, '62')) {
                $phoneCandidates[] = '0' . substr($phoneDigits, 2);
            } elseif (str_starts_with($phoneDigits, '0')) {
                $phoneCandidates[] = '62' . substr($phoneDigits, 1);
            }

            $phoneCandidates = array_values(array_unique(array_filter($phoneCandidates)));
        }

        $user = LoginUser::where(function ($query) use ($identifier, $phoneCandidates) {
            $query->where('namauser', $identifier)
                ->orWhere('kodelogin', $identifier)
                ->orWhere('no_hp', $identifier);

            if (!empty($phoneCandidates)) {
                $query->orWhereIn('no_hp', $phoneCandidates);
            }
        })
            ->orWhereHas('pegawai', function ($query) use ($identifier, $phoneCandidates) {
                $query->where('email', $identifier);

                if (!empty($phoneCandidates)) {
                    $query->orWhereIn('no_handphone', $phoneCandidates);
                } else {
                    $query->orWhere('no_handphone', $identifier);
                }
            })
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        if (!Hash::check($credentials['katasandi'], $user->katasandi)) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        // Ambil data pegawai
        $pegawai = DB::table('pegawai_m as pg')
            ->join('kelas_m as kls', 'pg.kelasfk', '=', 'kls.id')
            ->where('pg.id', $user->objectpegawaifk)
            ->select(
                'pg.id as pegawai_id',
                'pg.namalengkap as nama_pegawai',
                'kls.id as kelas_id',
                'kls.kelas as nama_kelas',
                'pg.foto as foto_pegawai',
                'pg.is_aktif'
            )
            ->first();

        if ($pegawai && !$pegawai->is_aktif) {
            return response()->json(['message' => 'Login gagal atau pegawai tidak aktif'], 401);
        }

        // Ambil user setting
        $userSetting = DB::table('user_settings')->where('user_id', $user->id)->first();

        // Tambahkan theme dan user_id ke objek pegawai
        if ($pegawai) {
            $pegawai->theme = $userSetting ? $userSetting->theme_mode : 'light'; // default to light
            $pegawai->user_id = $user->id;
        }

        $token = auth()->login($user);

        return $this->respondWithToken($token, $pegawai);
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $user = Auth::user();
        $token = auth()->login($user);

        $pegawai = DB::table('pegawai_m as pg')
            ->join('kelas_m as kls', 'pg.kelasfk', '=', 'kls.id')
            ->where('pg.id', $user->objectpegawaifk)
            ->select(
                'pg.id as pegawai_id',
                'pg.namalengkap as nama_pegawai',
                'kls.id as kelas_id',
                'kls.kelas as nama_kelas'
            )
            ->first();

        return $this->respondWithToken($token, $pegawai);
    }

    protected function respondWithToken($token, $pegawai)
    {
        return response()->json([
            'meta' => [
                'access_token' => $token,
                'token_type' => 'bearer',
            ],
            'data' => [
                'pegawai' => $pegawai,
                // 'expires_in' => auth()->factory()->getTTL() * 1
            ]
        ]);
    }

    public function createOtpRequest(Request $request)

    {

        $user = DB::table('loginuser_m')->where('namauser', $request->input('userID'))->first();

        if (!$user) {
            return response()->json(['error' => 'User Tidak ditemukan'], 404);
        }

        $pegawai = DB::table('pegawai_m')->where('id', $user->objectpegawaifk)->first();

        if (!$pegawai || empty($pegawai->email)) {
            return response()->json(['error' => 'Email untuk user ini tidak ditemukan'], 404);
        }

        $timeNow = Carbon::now('Asia/Jakarta');

        $todayOtpCount = DB::table('otp_requests')
            ->where('user_id', $user->id)
            ->whereDate('created_at', $timeNow->toDateString())
            ->count();

        if ($todayOtpCount >= 2) {
            return response()->json(['error' => 'Anda telah mencapai limit permintaan OTP untuk hari ini.'], 429);
        }

        $existingOtp = DB::table('otp_requests')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', $timeNow)
            ->latest('created_at')
            ->first();

        if ($existingOtp) {
            $expiresAt = Carbon::parse($existingOtp->expires_at);
            $timeRemaining = $timeNow->diffInSeconds($expiresAt, false);

            return response()->json(['error' => 'Anda baru saja meminta OTP. Silakan tunggu ' . max(0, $timeRemaining) . ' detik sebelum meminta lagi.'], 429);
        }

        try {

            $otp = rand(10000, 99999);
            $timeOtp = Carbon::now('Asia/Jakarta');
            $expiresAt = $timeOtp->copy()->addMinutes(1);

            $otpId = DB::table('otp_requests')->insertGetId([
                'user_id' => $user->id,
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'created_at' => $timeOtp,
                'status' => 'pending',
            ]);

            Mail::to($pegawai->email)->send(new OtpMail($otp));

            $response = response()->json([
                'ok' => true,
                'message' => 'OTP berhasil dikirim',
                'otp_id' => $otpId,
                'user_id' => $user->id,
                'user_email' => $pegawai->email,
            ]);

            return $response->cookie('otp_id', $otpId, 5);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function verifyOtp(Request $request)
    {
        $otpRequest = OtpRequest::where('otp', $request['otp'])->first();

        $timeNow = Carbon::now('Asia/Jakarta');

        if (!$otpRequest) {
            return response()->json(['message' => 'OTP not found'], 404);
        }

        if ($otpRequest->expires_at < $timeNow) {
            return response()->json(['status' => 'expired', 'message' => 'OTP expired'], 400);
        }

        if ($otpRequest->status === 'used') {
            return response()->json(['status' => 'used', 'message' => 'OTP already used'], 400);
        }

        $otpRequest->status = 'used';
        $otpRequest->updated_at = $timeNow;
        $otpRequest->save();

        return response()->json(['ok' => true, 'message' => 'OTP verified successfully']);
    }

    public function resendOtp(Request $request)
    {
        $timeupdatedOtp = Carbon::now('Asia/Jakarta');
        $expiresAt = $timeupdatedOtp->copy()->addMinutes(1);

        $otpRequest = OtpRequest::where('id', $request['otpId'])->first();

        if (!$otpRequest) {
            return response()->json(['message' => 'OTP request not found'], 404);
        }

        if ($otpRequest->attempts >= 5) {
            return response()->json([
                'error' => 'Anda telah melebihi batas pengiriman OTP',
                'max_attempts' => true
            ], 400);
        }

        $user = LoginUser::where('id', $otpRequest->user_id)->first();
        if (!$user) {
            return response()->json(['error' => 'User for OTP request not found'], 404);
        }

        $pegawai = DB::table('pegawai_m')->where('id', $user->objectpegawaifk)->first();

        if (!$pegawai || empty($pegawai->email)) {
            return response()->json(['error' => 'Email untuk user ini tidak ditemukan'], 404);
        }

        $newOtp = rand(10000, 99999);

        Mail::to($pegawai->email)->send(new OtpMail($newOtp));

        $otpRequest->update([
            'otp' => $newOtp,
            'expires_at' => $expiresAt,
            'status' => 'pending',
            'updated_at' => $timeupdatedOtp,
        ]);

        DB::table('otp_requests')->where('id', $request['otpId'])->increment('attempts');

        return response()->json([
            'ok' => true,
            'message' => 'OTP telah dikirim ulang',
            'expires_at' => $otpRequest->expires_at,
        ]);
    }
    public function updatePassword(Request $request)
    {
        $updatedAt = Carbon::now('Asia/Jakarta');

        $request->validate([
            'otpId' => 'required|exists:otp_requests,id',
            'idUser' => 'required|exists:loginuser_m,id',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = LoginUser::find($request->idUser);

        // $otpValid = OtpRequest::where('id', $request->otpId)
        //     ->where('user_id', $user->id)
        //     ->where('expires_at', '>', now())
        //     ->exists();

        // if (!$otpValid) {
        //     return response()->json(['error' => 'OTP tidak valid atau sudah kadaluarsa'], 400);
        // }

        $user->katasandi = Hash::make($request->new_password);
        $user->updated_at = $updatedAt;
        $user->save();

        return response()->json(['message' => 'Password berhasil diperbarui']);
    }
    public function changeUserPassword(Request $request)
    {
        $privateKeyPem = Storage::disk('local')->get('keys/private.pem');
        $privateKey = openssl_pkey_get_private($privateKeyPem);

        $encryptedPayload = base64_decode($request->input('payload'));
        if (!openssl_private_decrypt($encryptedPayload, $decrypted, $privateKey)) {
            return response()->json(['error' => 'Gagal mendekripsi data'], 400);
        }

        $data = json_decode($decrypted, true);
        if ($data === null) {
            return response()->json(['error' => 'Payload tidak valid'], 400);
        }

        $rules = [
            'username' => 'nullable|string|max:255',
        ];

        if (!empty($data['old_password']) || !empty($data['new_password']) || !empty($data['confirm_password'])) {
            $rules['old_password'] = 'required|min:8';
            $rules['new_password'] = 'required|min:8|same:confirm_password';
            $rules['confirm_password'] = 'required|min:8';
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $user = LoginUser::find(Auth::id());

        if (!empty($data['old_password']) && !Hash::check($data['old_password'], $user->katasandi)) {
            return response()->json(['error' => 'Password lama salah'], 400);
        }

        if (!empty($data['new_password'])) {
            $user->katasandi = Hash::make($data['new_password']);
        }

        if (!empty($data['username']) && $data['username'] !== $user->kodelogin) {
            $exists = LoginUser::where(function ($query) use ($data, $user) {
                $query->where('kodelogin', $data['username'])
                    ->orWhere('namauser', $data['username']);
            })
                ->where('id', '<>', $user->id)
                ->exists();

            if ($exists) {
                return response()->json(['error' => 'Username sudah dipakai'], 400);
            }

            $user->namauser = $data['username'];
        }

        $user->save();

        return response()->json(['message' => 'Password dan username berhasil diperbarui']);
    }
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {

            $otp = random_int(100000, 999999);

            cache()->put(
                'otp_' . $request->email,
                $otp,
                now()->addMinutes(5)
            );

            Mail::to($request->email)->send(new OtpMail($otp));

            return response()->json([
                'message' => 'OTP berhasil dikirim',
                'expired_in' => '5 menit'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
