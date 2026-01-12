<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class UsersCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Users::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');

        // Правила валідації
        $this->crud->setValidation([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,{{id}}',
            'phone' => [
                'nullable',
                'regex:/^380[0-9]{9}$/',
                'unique:users,phone,{{id}}',
            ],
            'password' => 'nullable|string|min:12',
            'department_id' => 'nullable|exists:users_department,id',
            'position_id' => 'nullable|exists:users_positions,id',
        ], [
            'email.unique' => 'Ця email-адреса вже використовується.',
            'phone.unique' => 'Цей номер телефону вже зареєстровано.',
            'phone.regex' => 'Номер телефону має бути у форматі 380123456789.',
        ]);

//        CRUD::denyAccess('show');
        CRUD::denyAccess('delete');
    }

    protected function setupListOperation()
    {
//        CRUD::column('id')->label('id');
        CRUD::column('name')->label('ФИО');
        CRUD::column('email')->label('Email');
//        CRUD::column('phone_formatted')->label('Телефон'); // Використовуємо аксессор
        CRUD::column('department_id')->label('Відділ')->type('select')->entity('department')->attribute('name');
        CRUD::column('position_id')->label('Должность')->type('select')->entity('position')->attribute('name');
//        CRUD::column('is_force_change_password')->type('boolean')->label('Force Password Change');
        CRUD::column('is_user')
            ->label('Користувач')
            ->type('closure')
            ->function(function ($entry) {
                return '<div class="text-center">' .
                    ($entry->is_user
                        ? '<i class="la la-check text-success" title="Так"></i>'
                        : '<i class="la la-times text-danger" title="Ні"></i>') .
                    '</div>';
            })
            ->escaped(false);

        CRUD::column('is_admin')
            ->label('Адмін')
            ->type('closure')
            ->function(function ($entry) {
                return '<div class="text-center">' .
                    ($entry->is_admin
                        ? '<i class="la la-check text-success" title="Так"></i>'
                        : '<i class="la la-times text-danger" title="Ні"></i>') .
                    '</div>';
            })
            ->escaped(false);

        CRUD::column('is_super_admin')
            ->label('Супер-адмін')
            ->type('closure')
            ->function(function ($entry) {
                return '<div class="text-center">' .
                    ($entry->is_super_admin
                        ? '<i class="la la-check text-success" title="Так"></i>'
                        : '<i class="la la-times text-danger" title="Ні"></i>') .
                    '</div>';
            })
            ->escaped(false);
    }

    protected function setupCreateOperation()
    {
        CRUD::field('last_name')->label('Прізвище')->type('text')->wrapper(['class' => 'form-group col-md-4'])->attributes(['id' => 'last_name']);
        CRUD::field('first_name')->label("Ім'я")->type('text')->wrapper(['class' => 'form-group col-md-4'])->attributes(['id' => 'first_name']);
        CRUD::field('middle_name')->label('По батькові')->type('text')->wrapper(['class' => 'form-group col-md-4'])->attributes(['id' => 'middle_name']);
        CRUD::field('name')
            ->label('ПІБ')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-12'])
            ->attributes(['id' => 'name', 'disabled' => 'disabled']);
        CRUD::field('email')
            ->label('Email')
            ->type('email')
            ->attributes(['id' => 'email', 'placeholder' => 'user@domain.com'])
            ->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('phone')
            ->label('Телефон')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->attributes(['id' => 'phone', 'placeholder' => '+38(000)000-00-00'])
            ->hint("Введіть номер телефону у форматі +38(012)345-67-89");
        CRUD::field('password')->label('Пароль')->type('password')->wrapper(['class' => 'form-group col-md-12'])->hint('Залиште порожнім для автоматичної генерації');
        CRUD::field('department_id')
            ->label('Відділ')
            ->type('select')
            ->entity('department')
            ->model('App\Models\UsersDepartment')
            ->attribute('name')
            ->scope('whereNull', 'deleted_at')
            ->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('position_id')
            ->label('Посада')
            ->type('select')
            ->entity('position')
            ->model('App\Models\UsersPosition')
            ->attribute('name')
            ->scope('whereNull', 'deleted_at')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('is_employee')
            ->label('Внутрішній працівник')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('is_user')
            ->label('Користувач')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-3'])
            ->default(true); // Встановлюємо значення за замовчуванням
        CRUD::field('is_admin')
            ->label('Адмін')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-3']);
        // Перевіряємо, чи поточний користувач має права створювати супер-адмінів
        if (!backpack_user()->is_super_admin) {
            // Якщо поточний користувач не є супер-адміном, забороняємо встановлення прапорця is_super_admin
            CRUD::field('is_super_admin')
                ->label('Супер-адмін')
                ->type('checkbox')
                ->wrapper(['class' => 'form-group col-md-3'])
                ->attributes(['disabled' => 'disabled']); // Вимикаємо поле
        } else {
            CRUD::field('is_super_admin')
                ->label('Супер-адмін')
                ->type('checkbox')
                ->wrapper(['class' => 'form-group col-md-3']);
        }
        CRUD::field('is_change_password')
            ->label('Дата останньої зміни пароля')
            ->type('date')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->attributes(['disabled' => 'disabled'])
            ->hint('Автоматично встановлюється поточна дата');
        CRUD::field('is_force_change_password')
            ->label('Примусова зміна пароля')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('custom_js')
            ->type('custom_html')
            ->value('
                <script src="' . \Illuminate\Support\Facades\Vite::asset('resources/js/admin/users_form_name.js') . '"></script>
                <script src="' . \Illuminate\Support\Facades\Vite::asset('resources/js/admin/users_form_input_phone.js') . '"></script>
                <script src="' . \Illuminate\Support\Facades\Vite::asset('resources/js/admin/users_form_email.js') . '"></script>

            ');
    }

    protected function setupUpdateOperation()
    {
        $user = $this->crud->getCurrentEntry();
        if ($user->is_super_admin && !backpack_user()->is_super_admin) {
            abort(403, 'У вас немає прав для редагування супер-адмінів.');
        }

        CRUD::field('last_name')->label('Прізвище')->type('text')->wrapper(['class' => 'form-group col-md-4'])->attributes(['id' => 'last_name']);
        CRUD::field('first_name')->label("Ім'я")->type('text')->wrapper(['class' => 'form-group col-md-4'])->attributes(['id' => 'first_name']);
        CRUD::field('middle_name')->label('По батькові')->type('text')->wrapper(['class' => 'form-group col-md-4'])->attributes(['id' => 'middle_name']);
        CRUD::field('name')
            ->label('ПІБ')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-12'])
            ->attributes(['id' => 'name', 'disabled' => 'disabled']);
        CRUD::field('email')
            ->label('Email')
            ->type('email')
            ->attributes(['id' => 'email', 'placeholder' => 'user@domain.com'])

            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('phone')
            ->label('Телефон')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->attributes(['id' => 'phone', 'placeholder' => '+38(000)000-00-00'])
            ->value($user->phone_formatted ?? '-') // Дефіс, якщо значення порожнє
            ->hint("Введіть номер телефону у форматі +38(012)345-67-89");

        CRUD::field('department_id')
            ->label('Відділ')
            ->type('select')
            ->entity('department')
            ->model('App\Models\UsersDepartment')
            ->attribute('name')
            ->scope('whereNull', 'deleted_at')
            ->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('position_id')
            ->label('Посада')
            ->type('select')
            ->entity('position')
            ->model('App\Models\UsersPosition')
            ->attribute('name')
            ->scope('whereNull', 'deleted_at')
            ->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('is_employee')
            ->label('Внутрішній працівник')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('is_user')
            ->label('Користувач')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('is_admin')
            ->label('Адмін')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-3']);
        CRUD::field('is_super_admin')
            ->label('Супер-адмін')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-3']);

        // Перевіряємо, чи є поточний користувач супер-адміном
        if (backpack_user()->is_super_admin) {
            CRUD::field('password')
                ->label('Новий пароль')
                ->type('password')
                ->wrapper(['class' => 'form-group col-md-6'])
                ->hint('Введіть новий пароль для користувача');
        } else {
            CRUD::field('password_display')
                ->label('Пароль')
                ->type('text')
                ->value('****************')
                ->attributes(['disabled' => 'disabled'])
                ->wrapper(['class' => 'form-group col-md-6']);
        }

        CRUD::field('is_change_password')
            ->label('Дата останньої зміни пароля')
            ->type('date')
            ->attributes(['disabled' => 'disabled'])
            ->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('is_force_change_password')
            ->label('Примусова зміна пароля')
            ->type('checkbox')
            ->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('change_password')
            ->label('Вимагати зміну пароля')
            ->type('custom_html')
            ->value('<a href="' . url('admin/user/require-password-change/' . $this->crud->getCurrentEntryId()) . '" class="btn btn-primary">Вимагати зміну пароля</a>')
            ->wrapper(['class' => 'form-group col-md-12']);
        CRUD::field('custom_js')
            ->type('custom_html')
            ->value('
                <script src="' . \Illuminate\Support\Facades\Vite::asset('resources/js/admin/users_form_name.js') . '"></script>
                <script src="' . \Illuminate\Support\Facades\Vite::asset('resources/js/admin/users_form_input_phone.js') . '"></script>
                <script src="' . \Illuminate\Support\Facades\Vite::asset('resources/js/admin/users_form_email.js') . '"></script>
            ');
    }

    protected function generatePassword($length = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function store()
    {
        $request = $this->crud->getRequest();
        $data = $request->all();

        // Зберігаємо оригінальний номер телефону для відображення
        $originalPhone = $data['phone'] ?? null;

        // Нормалізуємо телефон перед валідацією
        if (!empty($data['phone'])) {
            $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
            $request->merge(['phone' => $data['phone']]);
        } else {
            $data['phone'] = null;
            $request->merge(['phone' => null]);
        }

        // Валідація
        try {
            $request->validate([
                'last_name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => [
                    'nullable',
                    'regex:/^380[0-9]{9}$/',
                    'unique:users,phone',
                ],
                'password' => 'nullable|string|min:12',
                'department_id' => 'nullable|exists:users_department,id',
                'position_id' => 'nullable|exists:users_position,id',
            ], [
                'email.unique' => 'Ця email-адреса вже використовується.',
                'phone.unique' => 'Цей номер телефону вже зареєстровано.',
                'phone.regex' => 'Номер телефону має бути у форматі 380123456789.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Повертаємо оригінальний номер телефону у форму
            if ($originalPhone) {
                $request->merge(['phone' => $originalPhone]);
            }
            throw $e;
        }

        // Генерація пароля
        if (empty($data['password'])) {
            $generatedPassword = $this->generatePassword();
            $data['password'] = bcrypt($generatedPassword);
            \Alert::success("Користувач створений. Згенерований пароль: {$generatedPassword}")->flash();
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        // Обробка department_id і position_id
        $department = \App\Models\UsersDepartment::find($request->input('department_id'));
        $position = \App\Models\UsersPosition::find($request->input('position_id'));
        $data['is_employee'] = ($department && $department->is_employee) || ($position && $position->is_employee) ? 1 : 0;

        // Формуємо ПІБ
        $lastName = trim($data['last_name'] ?? '');
        $firstName = trim($data['first_name'] ?? '');
        $middleName = trim($data['middle_name'] ?? '');

        $formattedName = $lastName;
        if ($firstName) {
            $formattedName .= ' ' . mb_strtoupper(mb_substr($firstName, 0, 1)) . '.';
        }
        if ($middleName) {
            $formattedName .= mb_strtoupper(mb_substr($middleName, 0, 1)) . '.';
        }
        $data['name'] = $formattedName;

        $item = $this->crud->create($data);
        return $this->crud->performSaveAction($item->getKey());
    }

    public function update()
{
    $request = $this->crud->getRequest();
    $data = $request->all();

    // Зберігаємо оригінальний номер телефону для відображення
    $originalPhone = $data['phone'] ?? null;

    // Нормалізуємо телефон перед валідацією
    if (!empty($data['phone'])) {
        $normalizedPhone = preg_replace('/[^0-9]/', '', $data['phone']);
        // Перевіряємо, чи номер повний (12 цифр)
        if (strlen($normalizedPhone) < 12) {
            // Якщо номер неповний, додаємо помилку
            $request->merge(['phone' => $normalizedPhone]);
            $validator = \Validator::make($request->all(), [
                'phone' => 'size:12',
            ], [
                'phone.size' => 'Номер телефону введено не повністю.',
            ]);
            if ($validator->fails()) {
                // Повертаємо оригінальний номер телефону у форму
                $request->merge(['phone' => $originalPhone]);
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }
        $data['phone'] = $normalizedPhone;
        $request->merge(['phone' => $data['phone']]);
    } else {
        $data['phone'] = null;
        $request->merge(['phone' => null]);
    }

    // Отримуємо ID поточного користувача
    $userId = $request->get($this->crud->model->getKeyName());

    // Валідація
    try {
        $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => "required|email|unique:users,email,{$userId}",
            'phone' => [
                'nullable',
                'regex:/^380[0-9]{9}$/',
                "unique:users,phone,{$userId}",
            ],
            'password' => 'nullable|string|min:12',
            'department_id' => 'nullable|exists:users_department,id',
            'position_id' => 'nullable|exists:users_position,id',
        ], [
            'email.unique' => 'Ця email-адреса вже використовується.',
            'phone.unique' => 'Цей номер телефону вже зареєстровано.',
            'phone.regex' => 'Номер телефону має бути у форматі 380123456789.',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Повертаємо оригінальний номер телефону у форму
        if ($originalPhone) {
            $request->merge(['phone' => $originalPhone]);
        }
        throw $e;
    }

    // Обробка department_id і position_id
    $department = \App\Models\UsersDepartment::find($request->input('department_id'));
    $position = \App\Models\UsersPosition::find($request->input('position_id'));
    $data['is_employee'] = ($department && $department->is_employee) || ($position && $position->is_employee) ? 1 : 0;

    // Формуємо ПІБ
    $lastName = trim($data['last_name'] ?? '');
    $firstName = trim($data['first_name'] ?? '');
    $middleName = trim($data['middle_name'] ?? '');

    $formattedName = $lastName;
    if ($firstName) {
        $formattedName .= ' ' . mb_strtoupper(mb_substr($firstName, 0, 1)) . '.';
    }
    if ($middleName) {
        $formattedName .= mb_strtoupper(mb_substr($middleName, 0, 1)) . '.';
    }
    $data['name'] = $formattedName;

    // Обробка пароля для супер-адміна
    if (backpack_user()->is_super_admin && !empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
        $data['is_change_password'] = now();
        $data['is_force_change_password'] = false;
        \Alert::success('Пароль користувача успішно оновлено.')->flash();
    } else {
        unset($data['password']);
    }
    session()->flash('message', 'Запис успішно створено.');
    session()->flash('type', 'success');
    $item = $this->crud->update($request->get($this->crud->model->getKeyName()), $data);
    return $this->crud->performSaveAction($item->getKey());
}

    public function requirePasswordChange($id)
    {
        // Перевіряємо, чи є поточний користувач адміном або супер-адміном
        if (!backpack_user()->is_admin && !backpack_user()->is_super_admin) {
            \Alert::error('У вас немає прав для виконання цієї дії.')->flash();
            return redirect('admin/user');
        }

        $user = \App\Models\Users::findOrFail($id);
        $user->update([
            'is_force_change_password' => true,
        ]);
        \Alert::success('Користувачу потрібно змінити пароль при наступному вході.')->flash();
        return redirect('admin/user');
    }
}