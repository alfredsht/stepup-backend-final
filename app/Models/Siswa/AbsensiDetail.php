<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AbsensiDetail extends Model
{
    use HasFactory;

    protected $table = 'absensidetails_m';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'objectsiswafk',
        'waktu_tap',
        'status',
    ];
    
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Siswa\Siswa::class, 'objectsiswafk');
    }
}
