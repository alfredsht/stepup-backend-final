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
        Schema::create('notifikasi_m', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('statusenabled')->default(true);
            $table->string('kdprofile');


            $table->string('notif_type', 100);
            $table->string('notif_title', 255)->nullable();
            $table->text('notif_detail');

            $table->boolean('is_forall')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('pegawai_m')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi_m');
    }
};
