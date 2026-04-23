<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    use HasFactory;

    protected $table="agama_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'agama'
    ];
}
