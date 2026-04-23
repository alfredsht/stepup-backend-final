<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tahun_ajaran_m', function (Blueprint $table) {
            if (!Schema::hasColumn('tahun_ajaran_m', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable()->after('semester');
            }

            if (!Schema::hasColumn('tahun_ajaran_m', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            }

            if (!Schema::hasColumn('tahun_ajaran_m', 'is_aktif')) {
                $table->boolean('is_aktif')->default(false)->after('statusenabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajaran_m', function (Blueprint $table) {
            if (Schema::hasColumn('tahun_ajaran_m', 'tanggal_mulai')) {
                $table->dropColumn('tanggal_mulai');
            }

            if (Schema::hasColumn('tahun_ajaran_m', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }

            if (Schema::hasColumn('tahun_ajaran_m', 'is_aktif')) {
                $table->dropColumn('is_aktif');
            }
        });
    }
};
