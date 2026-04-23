<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration untuk membuat tabel otp_requests.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('otp_requests', function (Blueprint $table) {
            $table->id(); // Auto increment ID
            $table->string('user_id'); // Identitas pengguna (misalnya email, nomor telepon)
            $table->string('otp', 10); // OTP yang dihasilkan
            $table->timestamp('expires_at'); // Waktu kadaluarsa OTP
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending'); // Status OTP
            $table->integer('attempts')->default(0); // Jumlah percobaan memasukkan OTP (opsional)
            $table->timestamps(); // Timestamp untuk created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_requests');
    }
};