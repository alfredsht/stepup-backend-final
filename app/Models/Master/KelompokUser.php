<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokUser extends Model
{
    use HasFactory;
    protected $table = "kelompokuser_m";

    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'kelompokuser'
    ];

    public function menus()
    {
        return $this->belongsToMany(\App\Models\Admin\MappingMenu::class, 'role_menu', 'kelompokuserfk', 'menufk')
            ->withPivot(['can_view', 'can_add', 'can_edit', 'can_delete'])
            ->withTimestamps();
    }
}
