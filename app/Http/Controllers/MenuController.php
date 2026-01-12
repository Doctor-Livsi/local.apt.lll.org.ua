<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function getMenu()
    {
        $menuItems = MenuItem::with('childrenRecursive')
            ->whereNull('parent_id')
            ->whereNull('deleted_at')
            ->orderBy('lft')
            ->get();

        return $menuItems;
    }
}