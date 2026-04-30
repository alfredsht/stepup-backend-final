<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\UserSetting;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function updateUserSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:loginuser_m,id',
            'theme' => 'required|string|in:light,dark',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            UserSetting::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'kdprofile' => '10'
                ],
                [
                    'theme_mode' => $request->theme
                ]
            );

            return response()->json(['message' => 'User settings updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update user settings', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOwnProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user || empty($user->objectpegawaifk)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pegawaiId = $user->objectpegawaifk;
        $pegawai = DB::table('pegawai_m')
            ->where('id', $pegawaiId)
            ->where('kdprofile', '10')
            ->first();

        if (!$pegawai) {
            return response()->json(['message' => 'Data pegawai tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'namalengkap' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('pegawai_m', 'email')->ignore($pegawaiId, 'id')
            ],
            'no_handphone' => 'required|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agamafk' => 'nullable|integer|exists:agama_m,id',
            'jeniskelaminfk' => 'nullable|integer|exists:jeniskelamin_m,id',
            'pendidikan_terakhirfk' => 'nullable|integer|exists:pendidikan_m,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        DB::table('pegawai_m')
            ->where('id', $pegawaiId)
            ->where('kdprofile', '10')
            ->update([
                'namalengkap' => $validated['namalengkap'],
                'email' => $validated['email'],
                'no_handphone' => $validated['no_handphone'],
                'alamat' => $validated['alamat'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'agamafk' => $validated['agamafk'] ?? null,
                'jeniskelaminfk' => $validated['jeniskelaminfk'] ?? null,
                'objectpendidikanterakhirfk' => $validated['pendidikan_terakhirfk'] ?? null,
                'updated_at' => now('Asia/Jakarta'),
            ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
        ], 200);
    }

    public function updateOwnProfilePhoto(Request $request)
    {
        $user = auth()->user();

        if (!$user || empty($user->objectpegawaifk)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:800',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pegawaiId = $user->objectpegawaifk;
        $pegawai = DB::table('pegawai_m')
            ->where('id', $pegawaiId)
            ->where('kdprofile', '10')
            ->first();

        if (!$pegawai) {
            return response()->json(['message' => 'Data pegawai tidak ditemukan'], 404);
        }

        if (empty(config('cloudinary.cloud_url'))) {
            return response()->json([
                'message' => 'Konfigurasi Cloudinary belum lengkap di server',
            ], 500);
        }

        $foto = $request->file('foto');

        try {
            $uploadResult = Cloudinary::uploadFile($foto->getRealPath(), [
                'folder' => 'foto_pegawai',
                'public_id' => uniqid('pegawai_profile_')
            ]);
            $fotoPath = $uploadResult->getSecurePath();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal upload foto ke Cloudinary',
                'error' => $e->getMessage(),
            ], 500);
        }

        if (!empty($pegawai->foto)) {
            $parsedUrl = parse_url($pegawai->foto);

            if (!empty($parsedUrl['path'])) {
                $pathParts = explode('/', ltrim($parsedUrl['path'], '/'));
                $publicIdWithExt = end($pathParts);
                $oldPublicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);

                if (!empty($oldPublicId)) {
                    try {
                        Cloudinary::destroy('foto_pegawai/' . $oldPublicId);
                    } catch (\Throwable $e) {
                        // Ignore delete failure because new image upload has succeeded.
                    }
                }
            }
        }

        DB::table('pegawai_m')
            ->where('id', $pegawaiId)
            ->where('kdprofile', '10')
            ->update([
                'foto' => $fotoPath,
                'updated_at' => now('Asia/Jakarta'),
            ]);

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui',
            'data' => [
                'foto' => $fotoPath,
            ]
        ], 200);
    }
}

