<?php

namespace App\Models\Apteks;

use Illuminate\Database\Eloquent\Model;

class ApteksWeekdayLocale extends Model
{
    // Таблиця: [IT].[dbo].[apteks_weekdays_locale]
    protected $table = 'apteks_weekdays_locale';

    // PK: week_num_day (1..7)
    protected $primaryKey = 'week_num_day';
    public $incrementing = false;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'week_num_day',
        'en_name',
        'ua_name',
        'ru_name',
    ];
}
