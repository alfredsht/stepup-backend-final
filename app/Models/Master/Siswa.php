<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'students_m';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'nis',
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
        'pekerjaanayahfk',
        'pekerjaanibufk',
        'statussiswafk',
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

    public function pekerjaanayah(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Pekerjaan::class, 'pekerjaanayahfk');
    }

    public function pekerjaanibu(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Pekerjaan::class, 'pekerjaanibufk');
    }

    public function statusSiswa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Siswa\StatusSiswa::class, 'statussiswafk');
    }

    public function nilaiSiswa()
    {
        return $this->hasMany(\App\Models\NilaiSiswa::class, 'siswa_nis', 'nis');
    }
}
