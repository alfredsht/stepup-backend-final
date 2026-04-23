<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPegawai extends Model
{
    use HasFactory;
    protected $table = 'jenispegawai_m';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'jenispegawai'
    ];
    
}
