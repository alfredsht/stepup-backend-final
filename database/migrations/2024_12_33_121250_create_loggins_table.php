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
        Schema::create('loginuser_m', function (Blueprint $table) {
            $table->id()->index();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->string('kodelogin');
            $table->string('katasandi');
            $table->string('namauser');
            $table->integer('kelompokuserfk')->unsigned();
            $table->integer('objectpegawaifk')->unsigned();
            $table->string('no_hp')->nullable();
            $table->foreign('kelompokuserfk')->references('id')->on('kelompokuser_m');
            $table->foreign('objectpegawaifk')->references('id')->on('pegawai_m');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loginuser_m');
    }
};
