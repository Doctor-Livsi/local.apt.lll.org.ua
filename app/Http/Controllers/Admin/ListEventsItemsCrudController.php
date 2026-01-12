<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;
use App\Models\Admin\ListEventsItems;

class ListEventsItemsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Admin\ListEventsItems::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/list-events-items');
        CRUD::setEntityNameStrings('Причина', 'Список Events Items');

        CRUD::denyAccess('delete');
        CRUD::denyAccess('show');

    }

    public function store()
    {
        $this->crud->validateRequest();
        $request = $this->crud->getRequest();

        $data = $request->except(['custom_link']);
        // Створюємо запис вручну
        $item = $this->crud->create($data);

        // Повертаємо через логіку Backpack — редірект, повідомлення, і т.д.
        return $this->crud->performSaveAction($item->getKey());
    }

    public function update()
    {
        $this->crud->validateRequest();
        $request = $this->crud->getRequest();

        $data = $request->except(['custom_link']);
        // Оновлюємо запис вручну
        $item = $this->crud->update($this->crud->getCurrentEntryId(), $data);

        // Використовуємо логіку Backpack для завершення дії
        return $this->crud->performSaveAction($item->getKey());
    }

    protected function setupListOperation()
    {
        CRUD::addClause('orderBy', 'lft', 'asc');

        CRUD::addColumn([
            'name' => 'text_with_indent',
            'label' => 'Назва',
            'type' => 'text',
            'escaped' => false,
        ]);

        CRUD::denyAccess('show');
//        CRUD::denyAccess('delete');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([]);

        CRUD::addField(['name' => 'name', 'label' => 'Назва', 'type' => 'text']);

        CRUD::addField([
            'name' => 'parent_id',
            'label' => 'Батьківський пункт',
            'type' => 'select_from_array',
            'options' => $this->getParentOptions(),
            'allows_null' => true,
        ]);
    }

    protected function getParentOptions()
    {
        $items = ListEventsItems::all();
        $result = [];
        $this->buildOptionsTree($items, null, '', $result);
        return $result;
    }

    protected function buildOptionsTree($items, $parentId = null, $prefix = '', &$result = [])
    {
        $children = $items->where('parent_id', $parentId)->sortBy('lft');
        foreach ($children as $item) {
            $result[$item->id] = $prefix . $item->name;
            $this->buildOptionsTree($items, $item->id, $prefix . '• ', $result);
        }
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        // Перед відображенням форми змінюємо значення link, якщо потрібно
        $entry = $this->crud->getCurrentEntry();
        if ($entry && !array_key_exists($entry->link, $this->getStaticRoutes()) && $entry->link !== 'custom') {
            $entry->link = 'custom';
        }
    }

    protected function setupReorderOperation()
    {
        CRUD::enableReorder('name', 3);
        CRUD::set('reorder.parent_field', 'parent_id');
    }

    protected function getStaticRoutes()
    {
        $staticRoutes = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (str_starts_with($uri, 'api') || str_starts_with($uri, 'admin')) {
                continue;
            }
            if (in_array('GET', $route->methods()) && !preg_match('/\{.*?\}/', $uri)) {
                $name = $route->getName();
                if ($name && str_starts_with($name, 'sanctum')) {
                    continue;
                }
                $staticRoutes[$uri] = $name ? "$name ($uri)" : $uri;
            }
        }
        return $staticRoutes;
    }
}
