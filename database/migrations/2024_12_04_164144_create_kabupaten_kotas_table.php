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
        Schema::create('kabupatenkota_m', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->string('kabupatenkota');
            $table->integer('objectprovinsifk')->unsigned();
            $table->foreign('objectprovinsifk')->references('id')->on('provinsi_m');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupatenkota_m');
    }
};
