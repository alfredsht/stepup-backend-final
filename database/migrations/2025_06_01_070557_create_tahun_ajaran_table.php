<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tahun_ajaran_m', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('kdprofile'); // kode unik untuk profil sekolah
            $table->boolean('statusenabled');
            $table->string('tahun'); // contoh: "2024/2025"
            $table->string('semester'); // contoh: "Ganjil" / "Genap"
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
