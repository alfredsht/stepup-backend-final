<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPegawai extends Model
{
    use HasFactory;

    protected $table = 'statuspegawai_m';

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'status_pegawai'
    ];
}
