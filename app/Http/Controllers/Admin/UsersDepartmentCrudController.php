<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class UsersDepartmentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\UsersDepartment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/users-department');
        CRUD::setEntityNameStrings('department', 'departments');
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Назва відділу');
        CRUD::column('is_employee')->type('boolean')->label('Внутрішня');
        CRUD::column('deleted_at')->label('Status');
    }

    protected function setupCreateOperation()
    {
        CRUD::field([
            'name' => 'name',
            'label' => 'Назва відділу',
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

        // Повертаємо через логіку Backpack — редірект, повідомлення, і т.д.
        return $this->crud->performSaveAction($item->getKey());
    }

    public function update()
    {
        // Получаем данные из запроса
        $request = $this->crud->getRequest();
        $data = $request->all();

        // Получаем текущую запись
        $department = $this->crud->getCurrentEntry();

        // Проверяем, меняется ли status на closed
        if ($request->input('status') === 'closed' && $department->status !== 'closed') {
            // Проверяем, есть ли связанные пользователи
            $usersCount = $department->users()->count();
            if ($usersCount > 0) {
                // Добавляем флэш-сообщение с предупреждением
                \Alert::warning("Цей відділ призначений {$usersCount} користувачам. Закриття відділу може вплинути на їхні записи.")->flash();
            }
        }

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