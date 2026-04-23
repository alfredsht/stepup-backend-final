<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    use HasFactory;
    protected $table = 'pekerjaan_m';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'pekerjaaan',
    ];
}
