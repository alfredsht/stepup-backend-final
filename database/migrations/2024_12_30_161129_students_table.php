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
        Schema::create('students_m', function (Blueprint $table) {
            $table->bigInteger('nis')->primary();
            $table->bigInteger('nisn')->nullable();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->string('namalengkap');
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir');
            $table->string('alamat');

            $table->unsignedBigInteger('provinsifk')->nullable();
            $table->unsignedBigInteger('kabkotafk')->nullable();
            $table->unsignedBigInteger('kecamatanfk')->nullable();
            $table->unsignedBigInteger('kelurahanfk')->nullable();

            $table->string('no_handphone');
            $table->integer('agamafk')->unsigned();
            $table->integer('jeniskelaminfk')->unsigned();
            $table->integer('kelasfk')->unsigned();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->integer('pekerjaanayahfk')->unsigned();
            $table->integer('pekerjaanibufk')->unsigned();
            $table->string('tahun_masuk')->nullable();
            $table->integer('statussiswafk')->unsigned();
            $table->string('foto')->nullable();
            $table->string('nfc_tag_id')->unique()->nullable();
            $table->timestamps();

            $table->foreign('provinsifk')->references('id')->on('provinsi_m');
            $table->foreign('kabkotafk')->references('id')->on('kabupatenkota_m');
            $table->foreign('kecamatanfk')->references('id')->on('kecamatan_m');
            $table->foreign('kelurahanfk')->references('id')->on('desakelurahan_m');
            $table->foreign('jeniskelaminfk')->references('id')->on('jeniskelamin_m');
            $table->foreign('kelasfk')->references('id')->on('kelas_m');
            $table->foreign('agamafk')->references('id')->on('agama_m');
            $table->foreign('pekerjaanayahfk')->references('id')->on('pekerjaan_m');
            $table->foreign('pekerjaanibufk')->references('id')->on('pekerjaan_m');
            $table->foreign('statussiswafk')->references('id')->on('statussiswa_m');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_m');
    }
};
