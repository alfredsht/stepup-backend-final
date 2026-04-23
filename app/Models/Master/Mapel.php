<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'mapel_m';

    protected $fillable = [
        'statusenabled',
        'kdprofile',
        'kode_mapel',
        'nama_mapel',
        'deskripsi',
    ];
}
