<?php

namespace App\Models\Apteks;

use Illuminate\Database\Eloquent\Model;

class ApteksSchedule extends Model
{
    // Таблиця: [IT].[dbo].[apteks_schedules]
    protected $table = 'apteks_schedules';

    protected $fillable = [
        'apteka_id',
        'week_num_day',
        'week_number',
        'year',
        'schedules',
        'start_at',
        'end_at',
        'date_at',
    ];

    protected $casts = [
        'apteka_id'    => 'int',
        'week_num_day' => 'int',
        'week_number'  => 'int',
        'year'         => 'int',
        'date_at'      => 'date',
        // start_at / end_at у MSSQL "time" — зручно лишити як string і форматувати вручну
    ];
}
