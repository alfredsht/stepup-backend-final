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
        Schema::create('kelompokuser_m', function (Blueprint $table) {
            $table->id()->index();
            $table->string('kdprofile');
            $table->boolean('statusenabled');
            $table->string('kempokuser');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompokuser_m');
    }
};
