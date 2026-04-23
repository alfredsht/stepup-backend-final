<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Pegawai;

class Logging extends Model
{
    use SoftDeletes;

    protected $table = 'logging_m';

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'tanggal_log',
        'jenis_log_id',
        'pegawai_id',
        'keterangan',
    ];

    /**
     * Relasi ke JenisLog
     */
    public function jenisLog()
    {
        return $this->belongsTo(JenisLog::class, 'jenis_log_id');
    }

    /**
     * Relasi ke Pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
