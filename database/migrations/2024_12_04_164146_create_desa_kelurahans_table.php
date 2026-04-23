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
        Schema::create('desakelurahan_m', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->string('desakelurahan');
            $table->integer('objectkecamatanfk')->unsigned();
            $table->integer('objectkabupatenfk')->unsigned();
            $table->integer('objectprovinsi')->unsigned();
            $table->integer('objectnegarafk')->unsigned();
            $table->string('kodepos')->nullable();
            $table->foreign('objectkecamatanfk')->references('id')->on('kecamatan_m');
            $table->foreign('objectkabupatenfk')->references('id')->on('kabupatenkota_m');
            $table->foreign('objectprovinsi')->references('id')->on('provinsi_m');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desakelurahan_m');
    }
};
