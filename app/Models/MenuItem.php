<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;
    protected $table = 'apteks_menu_items';

    protected $fillable = [
        'name', 'type', 'link', 'page_id', 'parent_id', 'lft', 'rgt', 'depth', 'icon'
    ];

    protected $dates = ['deleted_at'];

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id', 'id')->orderBy('lft');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id', 'id');
    }
}