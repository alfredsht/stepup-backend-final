<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    use HasFactory;
    protected $table="pendidikan_m";
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'pendidikan'
    ];
}
