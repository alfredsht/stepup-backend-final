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
        Schema::create('role_menu', function (Blueprint $table) {
            $table->id();
            $table->string('kdprofile')->nullable();
            $table->boolean('statusenabled')->default(true);
            $table->unsignedBigInteger('jenispegawaifk');
            $table->unsignedBigInteger('menufk');
            $table->boolean('can_view')->default(true);
            $table->boolean('can_add')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();

            $table->foreign('jenispegawaifk')->references('id')->on('jenispegawai_m')->onDelete('cascade');
            $table->foreign('menufk')->references('id')->on('mapping_menu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menu');
    }
};
