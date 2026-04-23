<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi_tabungan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kdprofile');
            $table->bigInteger('tabungan_id')->unsigned();
            $table->bigInteger('pegawai_id')->unsigned();
            $table->unsignedBigInteger('jenis_transaksi_id');
            $table->decimal('jumlah', 12, 2)->nullable(); 
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_transaksi')->useCurrent();
            $table->timestamps();

            $table->foreign('tabungan_id')->references('id')->on('tabungan_siswa')->onDelete('cascade');
            $table->foreign('pegawai_id')->references('id')->on('pegawai_m')->onDelete('cascade');
            $table->foreign('jenis_transaksi_id')->references('id')->on('jenis_transaksi_tabungan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_tabungan');
    }
};

