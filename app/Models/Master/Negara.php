<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    use HasFactory;
    protected $table="negara_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'negara'
    ];
}
