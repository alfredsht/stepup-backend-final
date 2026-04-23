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
        Schema::create('alamat_m', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->string('namajalankampung');
            $table->string('rt');
            $table->string('rw');
            $table->integer('objectdesakelurahanfk')->unsigned();
            $table->integer('objectkecmatanfk')->unsigned();
            $table->integer('objectkotakabupatenfk')->unsigned();
            $table->integer('objectprovinsifk')->unsigned();
            $table->integer('objectnegarafk')->unsigned();
            $table->foreign('objectdesakelurahanfk')->references('id')->on('desakelurahan_m');
            $table->foreign('objectkecmatanfk')->references('id')->on('kecamatan_m');
            $table->foreign('objectkotakabupatenfk')->references('id')->on('kabupatenkota_m');
            $table->foreign('objectprovinsifk')->references('id')->on('provinsi_m');
            $table->foreign('objectnegarafk')->references('id')->on('negara_m');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat_m');
    }
};
