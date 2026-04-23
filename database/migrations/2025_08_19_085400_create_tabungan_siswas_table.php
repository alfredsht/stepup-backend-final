<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tabungan_siswa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kdprofile');
            $table->bigInteger('student_id')->unsigned();
            $table->decimal('saldo', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('student_id')->references('nis')->on('students_m')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabungan_siswa');
    }
};
