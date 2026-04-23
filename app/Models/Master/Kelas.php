<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table="kelas_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'kelas'
    ];
}
