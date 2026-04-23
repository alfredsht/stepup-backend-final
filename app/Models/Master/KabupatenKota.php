<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KabupatenKota extends Model
{
    use HasFactory;

    protected $table="kabupatenkota_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'kabupataenkota',
        'objectprovinsifk'
    ];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Provinsi::class, 'objectprovinsifk');
    }
}
