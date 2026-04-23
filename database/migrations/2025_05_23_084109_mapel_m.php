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
        Schema::create('mapel_m', function (Blueprint $table) {
            $table->id(); 
            $table->boolean('statusenabled');
            $table->string('kdprofile');
            $table->string('kode_mapel', 10)->unique(); 
            $table->string('nama_mapel', 100); 
            $table->string('deskripsi')->nullable();
            $table->string('nama_singkat')->nullable();
            $table->timestamps(); 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel_m');
    }
};
