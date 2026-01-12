<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;
use App\Models\Admin\MenuItem;

class MenuItemCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Admin\MenuItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/menu-item');
        CRUD::setEntityNameStrings('Menu', 'Menu');
    }

    public function store()
    {
        $this->crud->validateRequest();
        $request = $this->crud->getRequest();

        // Якщо обрано "власне посилання"
        if ($request->input('link') === 'custom' && !empty($request->input('custom_link'))) {
            $request->merge(['link' => $request->input('custom_link')]);
        }

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

        // Якщо обрано "власне посилання"
        if ($request->input('link') === 'custom' && !empty($request->input('custom_link'))) {
            $request->merge(['link' => $request->input('custom_link')]);
        }

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

        CRUD::addColumn(['name' => 'icon', 'label' => 'Іконка']);
        CRUD::addColumn(['name' => 'link', 'label' => 'Посилання']);
        CRUD::addColumn(['name' => 'type', 'label' => 'Тип']);
        CRUD::denyAccess('show');
        CRUD::denyAccess('delete');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([]);

        CRUD::addField(['name' => 'name', 'label' => 'Назва', 'type' => 'text']);
        CRUD::addField(['name' => 'icon', 'label' => 'Іконка', 'type' => 'text']);
        CRUD::addField([
            'name' => 'link',
            'label' => 'Посилання',
            'type' => 'select_from_array',
            'options' => array_merge(['custom' => 'Власне посилання'], $this->getStaticRoutes()),
            'allows_null' => true,
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
            'default' => $this->crud->getCurrentEntry() && !array_key_exists($this->crud->getCurrentEntry()->link, $this->getStaticRoutes()) ? 'custom' : null,
        ]);
        CRUD::addField([
            'name' => 'custom_link',
            'label' => 'Власне посилання',
            'type' => 'text',
            'value' => $this->crud->getCurrentEntry() && !array_key_exists($this->crud->getCurrentEntry()->link, $this->getStaticRoutes()) ? $this->crud->getCurrentEntry()->link : '',
            'wrapper' => [
                'class' => 'form-group col-md-6 custom-link-field',
                'style' => 'display: none;',
            ],
        ]);
        CRUD::addField([
            'type' => 'custom_html',
            'name' => 'link_script',
            'value' => '
        <script>
                function toggleCustomLinkField() {
                    const linkField = document.querySelector("[name=\"link\"]");
                    const customLinkField = document.querySelector(".custom-link-field");
                    if (linkField.value === "custom") {
                        customLinkField.style.display = "block";
                    } else {
                        customLinkField.style.display = "none";
                        document.querySelector("[name=\"custom_link\"]").value = "";
                    }
                }
                document.addEventListener("DOMContentLoaded", toggleCustomLinkField);
                document.querySelector("[name=\"link\"]").addEventListener("change", toggleCustomLinkField);
            </script>
        ',
        ]);

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
        $items = MenuItem::all();
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
