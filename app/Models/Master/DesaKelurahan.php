<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesaKelurahan extends Model
{
    use HasFactory;

    protected $table="desakelurahan_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'desakelurahan',
        'objectkecamatanfk',
        'objecctkabupatenfk',
        'objectprovinsi',
        'kodepos'
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Kecamatan::class, 'objectkecamatanfk');
    }
}
