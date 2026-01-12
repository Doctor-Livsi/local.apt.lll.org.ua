<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class ListEventsItems extends Model
{
    use CrudTrait, SoftDeletes;

    protected $table = 'apteks_list_events_items';
    protected $fillable = ['name', 'link', 'parent_id', 'lft', 'depth', 'icon'];
    protected $dates = ['deleted_at'];
    protected static function booted(): void
    {
        static::created(function ($item) {
            $item->rebuildTree(); // Перебудова дерева після створення,
        });

        static::updated(function ($item) {
            $item->rebuildTree(); // Перебудова дерева після створення,
        });
    }

    public function rebuildTree()
    {
        $rootItems = self::whereNull('parent_id')->orderBy('id')->get();
        $position = 1;
        foreach ($rootItems as $root) {
            $position = $this->rebuildTreeNode($root, $position, 1); // depth = 1 для верхнього рівня
        }
    }

    protected function rebuildTreeNode($item, $left, $depth)
    {
        $item->lft = $left;
        $item->depth = $depth;
        $right = $left + 1;

        $children = $item->children()->orderBy('id')->get();
        foreach ($children as $child) {
            $right = $this->rebuildTreeNode($child, $right, $depth + 1);
        }

        $item->rgt = $right;
        $item->save();

        return $right + 1;
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ListEventsItems::class, 'parent_id');
    }

    public function syncDepthRecursively(): void
    {
        $this->depth = $this->parent ? $this->parent->depth + 1 : 0;
        $this->save();
        foreach ($this->children as $child) {
            $child->syncDepthRecursively();
        }
    }

    public function getTextWithIndentAttribute(): string
    {
        $adjustedDepth = max(0, $this->depth - 1);
        return str_repeat('• ', $adjustedDepth) . $this->name;
    }
}
