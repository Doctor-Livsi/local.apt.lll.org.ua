<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class UsersPositionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\UsersPosition::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/users-position');
        CRUD::setEntityNameStrings('position', 'positions');
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Position Name');
        CRUD::column('is_employee')->type('boolean')->label('Internal Position');
        CRUD::column('deleted_at')->label('Status');
    }

    protected function setupCreateOperation()
    {
        CRUD::field([
            'name' => 'name',
            'label' => 'Назва посади',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-12'],
        ]);
        CRUD::field([
            'name' => 'is_employee',
            'label' => 'Компанія',
            'type' => 'select_from_array',
            'options' => [
                1 => 'Внутрішня',
                0 => 'Зовнішня',
            ],
            'default' => 1,
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);
        CRUD::field([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [
                'active' => 'Діюча',
                'closed' => 'Припинена',
            ],
            'default' => 'active',
            'wrapper' => ['class' => 'form-group col-md-6'],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        // Получаем данные из запроса
        $request = $this->crud->getRequest();
        $data = $request->all();

        // Обрабатываем status для установки deleted_at
        if ($request->input('status') === 'closed') {
            $data['deleted_at'] = now();
        } else {
            $data['deleted_at'] = null;
        }

        // Создаём запись вручну
        $item = $this->crud->create($data);

        // Повертаємо через логіку Backpack — редірект, повідомлення, и т.д.
        return $this->crud->performSaveAction($item->getKey());
    }

    public function update()
    {
        // Получаем данные из запроса
        $request = $this->crud->getRequest();
        $data = $request->all();

        // Обрабатываем status для установки deleted_at
        if ($request->input('status') === 'closed') {
            $data['deleted_at'] = now();
        } else {
            $data['deleted_at'] = null;
        }

        // Обновляем запись вручну
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()), $data);

        // Повертаємо через логіку Backpack — редірект, повідомлення, и т.д.
        return $this->crud->performSaveAction($item->getKey());
    }
}