<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RoleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Role::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/role');
        CRUD::setEntityNameStrings('role', 'roles');
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Role Name');

        CRUD::addColumn([
            'name' => 'permissions',
            'label' => 'Permissions',
            'type' => 'select_multiple',
            'entity' => 'permissions',
            'attribute' => 'name',
            'model' => \Spatie\Permission\Models\Permission::class,
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(RoleRequest::class);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Role Name',
        ]);

        CRUD::addField([
            'label' => 'Permissions',
            'type' => 'checklist',
            'name' => 'permissions',
            'entity' => 'permissions',
            'model' => "Spatie\Permission\Models\Permission",
            'attribute' => 'name',
            'pivot' => true,
            'select_all' => true,
            'options' => (function () {
                return \Spatie\Permission\Models\Permission::all()
                    ->pluck('name', 'id')
                    ->toArray();
            }),
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}