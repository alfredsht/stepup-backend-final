<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mapjadwaltoguru_t', function (Blueprint $table) {
            $table->id()->index();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->integer('objectgurufk')->unsigned();
            $table->date('tanggal_belajar');
            $table->time('jam_mulai');
            $table->time('jam_akhir');
            $table->timestamp('tanggal_input');
            $table->string('keterangan')->nullable();
            $table->foreign('objectgurufk')->references('id')->on('pegawai_m');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapjadwaltoguru_t');
    }
};
