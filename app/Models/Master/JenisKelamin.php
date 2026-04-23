<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKelamin extends Model
{
    use HasFactory;
    protected $table="jeniskelamin_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'jeniskelamin',
    ];
}
