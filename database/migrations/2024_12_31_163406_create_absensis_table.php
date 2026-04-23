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
        Schema::create('absensi_m', function (Blueprint $table) {
            $table->id()->index();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->unsignedBigInteger('objectsiswafk'); 
            $table->timestamp('waktu_tap_in');
            $table->timestamp('waktu_tap_out')->nullable(); 
            $table->string('status'); 
            $table->string('keterangan')->nullable();
            $table->text('filebukti')->nullable(); // ⬅️ diperbaiki jadi text
            $table->string('status_tap'); 
            $table->timestamp('waktu_update');
            
            $table->foreign('objectsiswafk')->references('nis')->on('students_m');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_m');
    }
};
