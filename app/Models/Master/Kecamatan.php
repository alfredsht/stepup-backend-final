<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Kecamatan extends Model
{
    use HasFactory;

    protected $table="kecamatan_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'kecamatan',
        'objectkabupatenkotafk',
    ];

    public function kabupatenkota(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\KabupatenKota::class, 'objectkabupatenkotafk');
    }
}
