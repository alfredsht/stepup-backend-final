<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran_m';

    protected $fillable = [
        'tahun',
        'semester',
        'statusenabled',
        'is_aktif',
        'tanggal_mulai',
        'tanggal_selesai'
    ];
}
