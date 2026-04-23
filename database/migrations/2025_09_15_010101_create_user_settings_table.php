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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->string('kdprofile');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('loginuser_m')->onDelete('cascade');
            $table->string('theme_mode')->default('light');
            $table->boolean('notifications_enabled')->default(true);
            // Anda bisa menambahkan pengaturan lain di sini di masa mendatang
            // contoh: $table->string('language')->default('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
