<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiSiswaTable extends Migration
{
    public function up()
{
    Schema::create('nilai_siswa', function (Blueprint $table) {
        $table->id();
        
        $table->unsignedBigInteger('siswa_nis');
        $table->foreign('siswa_nis')->references('nis')->on('students_m')->onDelete('cascade');
        $table->unsignedBigInteger('mapel_id');
        $table->foreign('mapel_id')->references('id')->on('mapel_m')->onDelete('cascade');
        $table->unsignedBigInteger('kelas_id');
        $table->foreign('kelas_id')->references('id')->on('kelas_m')->onDelete('cascade');
        $table->unsignedBigInteger('tahun_ajaran_id');
        $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran_m')->onDelete('cascade');
        $table->unsignedBigInteger('guru_id')->nullable();
        $table->foreign('guru_id')->references('id')->on('pegawai_m')->onDelete('set null');
        $table->date('tanggal_penilaian');

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('nilai_siswa');
    }
}

