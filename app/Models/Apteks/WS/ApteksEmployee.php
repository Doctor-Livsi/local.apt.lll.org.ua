<?php

namespace App\Models\Apteks\WS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApteksEmployee extends Model
{
    use SoftDeletes;
    protected $connection = 'sqlsrv';
    protected $table = 'apteks_employees';

    protected $fillable = [
        'id_tochka',
        'apteka_id',
        'apteka_name',
        'person_rref',
        'fio_ua',
        'fio_ru',
        'code',
        'position',
        'phone',
        'sort',
        'on_working',
    ];

    protected $casts = [
        'apteka_id'   => 'integer',
        'sort'        => 'integer',
        'code'        => 'integer',
        'on_working'  => 'boolean',
    ];
}
