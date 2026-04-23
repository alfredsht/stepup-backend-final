<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Pegawai extends Model
{
    use HasFactory;
    protected $table = 'pegawai_m';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'namalengkap',
        'jeniskelaminfk',
        'no_handphone',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'agamafk',
        'kelasfk',
        'objectjenispegawaifk',
        'objectpendidikanterakhirfk',
        'objectnegarafk',
        'tahun_masuk'
    ];

    public function jeniskelamin(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\JenisKelamin::class, 'jeniskelaminfk');
    }
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Kelas::class, 'kelasfk');
    }

    public function agama(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Agama::class, 'agamafk');
    }

    public function negara(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Negara::class, 'objectnegarafk');
    }

    public function jenispegawai(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\JenisPegawai::class, 'objectjenispegawaifk');
    }

    public function pendidikan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Pendidikan::class, 'objectpendidikanterakhirfk');
    }

    public function statuspegawai(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\StatusPegawai::class, 'status_kepegawaian');
    }
}
