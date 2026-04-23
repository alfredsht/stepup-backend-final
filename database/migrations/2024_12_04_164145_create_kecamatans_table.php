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
        Schema::create('kecamatan_m', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->string('kecamatan');
            $table->integer('objectkabupatenkotafk')->unsigned();
            $table->foreign('objectkabupatenkotafk')->references('id')->on('kabupatenkota_m');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kecamatan_m');
    }
};
