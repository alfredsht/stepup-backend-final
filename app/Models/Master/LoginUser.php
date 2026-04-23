<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;


class LoginUser extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table = "loginuser_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'kodelogin',
        'katasandi',
        'kelompokuserfk',
        'namauser',
        'objectpegawaifk',
        'statuslogin',
        'no_hp',
    ];

    public function kelompokuser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\KelompokUser::class, 'kelompokuserfk');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Master\Pegawai::class, 'objectpegawaifk');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'pegawai_id' => $this->objectpegawaifk
        ];
    }

    public function menus()
    {
        return $this->belongsToMany(\App\Models\Admin\MappingMenu::class, 'user_menu', 'loginuserfk', 'menufk')
            ->withPivot(['can_view', 'can_add', 'can_edit', 'can_delete'])
            ->withTimestamps();
    }
}
