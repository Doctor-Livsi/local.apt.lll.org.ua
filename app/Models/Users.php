<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{
    use CrudTrait, HasApiTokens;
    protected $table = 'users';
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'name',
        'email',
        'phone',
        'password',
        'is_change_password',
        'is_force_change_password',
        'department_id',
        'position_id',
        'is_employee',
        'is_admin',
        'is_super_admin',
        'is_user',
    ];

    protected $casts = [
        'is_change_password' => 'date',
        'is_force_change_password' => 'boolean',
        'is_admin' => 'boolean',
        'is_super_admin' => 'boolean',
        'is_user' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(UsersDepartment::class, 'department_id');
    }

    public function position()
    {
        return $this->belongsTo(UsersPosition::class, 'position_id');
    }

    public function getNameAttribute()
    {
        $lastName = trim($this->last_name ?? '');
        $firstName = trim($this->first_name ?? '');
        $middleName = trim($this->middle_name ?? '');

        $formattedName = $lastName;

        if ($firstName) {
            $formattedName .= ' ' . mb_strtoupper(mb_substr($firstName, 0, 1)) . '.';
        }

        if ($middleName) {
            $formattedName .= mb_strtoupper(mb_substr($middleName, 0, 1)) . '.';
        }

        return $formattedName;
    }

    public function getPhoneFormattedAttribute()
    {
        if (empty($this->phone)) {
            return null;
        }

        $phone = $this->phone;
        if (strlen($phone) === 12) {
            return sprintf(
                '+%s(%s)%s-%s-%s',
                substr($phone, 0, 2),  // 380
                substr($phone, 2, 3),  // 99
                substr($phone, 5, 3),  // 123
                substr($phone, 8, 2),  // 45
                substr($phone, 10, 2)  // 67
            );
        }

        return $phone;
    }

    public function needsPasswordChange()
    {
        if ($this->is_force_change_password) {
            return true;
        }

        $daysSinceChange = $this->is_change_password->diffInDays(now());
        return $daysSinceChange >= 40;
    }
}