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
        Schema::create('logging_m', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->timestamp('tanggal_log')->useCurrent();
            $table->unsignedBigInteger('jenis_log_id');
            $table->unsignedBigInteger('pegawai_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('pegawai_id')->references('id')->on('pegawai_m')->onDelete('cascade');
            $table->foreign('jenis_log_id')->references('id')->on('jenis_log_m')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logging_m');
    }
};
