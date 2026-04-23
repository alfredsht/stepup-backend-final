<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateRSAKeys extends Command
{
    protected $signature = 'rsa:generate';
    protected $description = 'Generate RSA key pair for encryption/decryption';

    public function handle()
    {
        $res = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        // Export private key
        openssl_pkey_export($res, $privateKey);

        // Extract public key
        $publicKeyDetails = openssl_pkey_get_details($res);
        $publicKey = $publicKeyDetails["key"];

        // Simpan ke storage/app/keys
        file_put_contents(storage_path('app/keys/private.pem'), $privateKey);
        file_put_contents(storage_path('app/keys/public.pem'), $publicKey);


        $this->info('RSA key pair generated successfully!');
    }
}
