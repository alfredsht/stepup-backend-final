<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\Logging;

class JenisLog extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_log_m';

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'nama_log',
    ];


    public function logs()
    {
        return $this->hasMany(Logging::class, 'jenis_log_id');
    }
}
