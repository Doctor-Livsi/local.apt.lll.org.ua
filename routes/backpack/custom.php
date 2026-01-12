<?php

use Illuminate\Support\Facades\Route;
//use \App\Http\Controllers\Admin\ApteksListEventsCrudController;
//use \App\Http\Controllers\Admin\ApteksListCauseCrudController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('user', 'UsersCrudController');
    Route::crud('post', 'PostCrudController');
    Route::crud('role', 'RoleCrudController');
    Route::crud('permission', 'PermissionCrudController');

    Route::crud('menu-item', 'MenuItemCrudController'); 
    Route::crud('users-department', 'UsersDepartmentCrudController');
    Route::crud('users-position', 'UsersPositionCrudController');
    Route::crud('list-events-items', 'ListEventsItemsCrudController');
    Route::crud('list-cause-items', 'ListCauseItemsCrudController');
});

// this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
