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
        Schema::create('pegawai_m', function (Blueprint $table) {
            $table->id()->index();
            $table->string('kdprofile');
            $table->boolean('statusenabled');

            
            $table->string('namalengkap', 255);
            $table->integer('jeniskelaminfk')->unsigned();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->string('no_handphone', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email', 100)->nullable();

            
            $table->string('nik', 20)->nullable();
            $table->string('nip', 50)->nullable()->unique();
            $table->string('nuptk', 50)->nullable()->unique();

            // $table->string('status_kepegawaian', 50)->nullable(); 
            $table->unsignedBigInteger('status_kepegawaian')->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->date('tmt_jabatan')->nullable();
            $table->date('tmt_pengangkatan')->nullable();
            $table->string('sk_pengangkatan', 255)->nullable();

            
            $table->year('tahun_masuk')->nullable();
            $table->string('foto', 255)->nullable();

            
            $table->integer('agamafk')->unsigned();
            $table->integer('kelasfk')->unsigned();
            $table->integer('objectjenispegawaifk')->unsigned();
            $table->integer('objectpendidikanterakhirfk')->unsigned();
            $table->integer('objectnegarafk')->unsigned();

            
            $table->boolean('is_wali_kelas')->default(false);
            $table->boolean('is_aktif')->default(true);
            $table->unsignedBigInteger('kelas_wali')->nullable();

            
            $table->foreign('agamafk')->references('id')->on('agama_m');
            $table->foreign('jeniskelaminfk')->references('id')->on('jeniskelamin_m');
            $table->foreign('kelasfk')->references('id')->on('kelas_m');
            $table->foreign('objectjenispegawaifk')->references('id')->on('jenispegawai_m');
            $table->foreign('objectpendidikanterakhirfk')->references('id')->on('pendidikan_m');
            $table->foreign('objectnegarafk')->references('id')->on('negara_m');
            $table->foreign('kelas_wali')->references('id')->on('kelas_m');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_m');
    }
};
