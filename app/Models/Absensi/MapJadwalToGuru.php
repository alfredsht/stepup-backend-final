<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapJadwalToGuru extends Model
{
    use HasFactory;
    protected $table = 'mapjadwaltoguru_t';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'objectgurufk',
        'tanggal_belajar',
        'jam_mulai',
        'jam_akhir',
        'tanggal_input'
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Pegawai::class, 'objectgurufk');
    }
}
