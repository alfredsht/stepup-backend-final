<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rapot_m', function (Blueprint $table) {
            $table->id();
            $table->string('kdprofile')->default('10');
            $table->boolean('statusenabled')->default(true);

            $table->unsignedBigInteger('siswa_nis');
            $table->unsignedBigInteger('tahun_ajaran_id');

            $table->text('catatan_wali_kelas')->nullable();
            $table->boolean('is_finalized')->default(false);
            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->timestamp('finalized_at')->nullable();

            $table->timestamps();

            $table->foreign('siswa_nis')->references('nis')->on('students_m')->onDelete('cascade');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran_m')->onDelete('cascade');
            $table->foreign('finalized_by')->references('id')->on('loginuser_m')->nullOnDelete();

            $table->unique(['siswa_nis', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapot_m');
    }
};
