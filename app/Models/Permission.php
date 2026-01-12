<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use CrudTrait;

    // Цей метод використовує Backpack для відображення назви в чекбоксах
    public function identifiableAttribute()
    {
        return $this->name;
    }
}
