<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusSiswa extends Model
{
    use HasFactory;
    protected $table = 'statussiswa_m';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'status_siwa',
    ];
}
