<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingMenu extends Model
{
    protected $table = 'mapping_menu';
    protected $fillable = [
        'kdprofile',
        'statusenabled',
        'kode_menu',
        'nama_menu',
        'icon',
        'url',
        'parent_id',
        'urutan'
    ];

    public function children()
    {
        return $this->hasMany(MappingMenu::class, 'parent_id')->orderBy('urutan');
    }

    public function parent()
    {
        return $this->belongsTo(MappingMenu::class, 'parent_id');
    }
}
