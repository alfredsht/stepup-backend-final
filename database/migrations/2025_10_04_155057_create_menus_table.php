<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapping_menu', function (Blueprint $table) {
            $table->id();
            $table->string('kdprofile');
            $table->boolean('statusenabled')->default(true);
            $table->string('kode_menu')->nullable()->unique();
            $table->string('nama_menu', 150);
            $table->string('icon', 100)->nullable();
            $table->string('url', 255)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('mapping_menu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapping_menu');
    }
};
