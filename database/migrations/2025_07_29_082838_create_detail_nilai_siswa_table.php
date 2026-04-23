<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('detail_nilai_siswa', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('nilai_siswa_id');
            $table->foreign('nilai_siswa_id')->references('id')->on('nilai_siswa')->onDelete('cascade');

            $table->decimal('nilai_harian', 5, 2)->nullable();
            $table->decimal('nilai_pr', 5, 2)->nullable();
            $table->decimal('nilai_tugas', 5, 2)->nullable();
            $table->decimal('nilai_uts', 5, 2)->nullable();
            $table->decimal('nilai_uas', 5, 2)->nullable();
            $table->text('catatan_guru')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_nilai_siswa');
    }

};
