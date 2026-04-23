<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi_m';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'objectsiswafk',
        'tanggal_input',
        'status',
        'status_tap'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Siswa::class, 'objectsiswafk');
    }
}
