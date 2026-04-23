<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;


class EncryptApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientPublicKeyPemBase64 = $request->header('X-Client-Public-Key');
        if (!$clientPublicKeyPemBase64) {
            return $next($request); // Tidak ada public key => kirim plain
        }

        $clientPublicKeyPem = base64_decode($clientPublicKeyPemBase64); // decode base64 ke PEM

        $response = $next($request);

        // Hanya enkripsi jika content type-nya JSON
        $contentType = $response->headers->get('Content-Type');
        if (!str_contains($contentType, 'application/json')) {
            return $response;
        }

        $originalContent = $response->getContent();

        // === Generate AES Key & IV ===
        $aesKey = random_bytes(16); // AES-128 key
        $iv = random_bytes(16);     // 16 byte IV untuk AES-CBC

        // === Encrypt the response with AES ===
        $encryptedDataBinary = openssl_encrypt(
            $originalContent,
            'aes-128-cbc',
            $aesKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encryptedDataBinary === false) {
            return response()->json(['error' => 'AES encryption failed'], 500);
        }

        $encryptedData = base64_encode($iv . $encryptedDataBinary);

        // === Encrypt AES Key with Client's RSA Public Key ===
        $clientPublicKey = openssl_pkey_get_public($clientPublicKeyPem);
        if (!$clientPublicKey) {
            return response()->json(['error' => 'Invalid client public key'], 400);
        }

        $aesKeyEncrypted = null;
        $rsaSuccess = openssl_public_encrypt(
            $aesKey,
            $aesKeyEncrypted,
            $clientPublicKey,
            OPENSSL_PKCS1_OAEP_PADDING
        );

        if (!$rsaSuccess) {
            return response()->json(['error' => 'RSA encryption failed'], 500);
        }

        $encryptedKey = base64_encode($aesKeyEncrypted);

        // Return final encrypted response
        return response()->json([
            'encrypted_key' => $encryptedKey,
            'encrypted_data' => $encryptedData,
        ]);
    }
}
