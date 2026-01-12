<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apteks_weekdays_locale', function (Blueprint $table) {
            $table->unsignedTinyInteger('week_num_day')->primary(); // 1–7
            $table->string('en_name', 20);
            $table->string('ua_name', 20);
            $table->string('ru_name', 20);
        });

        // Початкове заповнення
        DB::table('apteks_weekdays_locale')->insert([
            ['week_num_day' => 1, 'ua_name' => 'Понеділок', 'en_name' => 'Monday', 'ru_name' => 'Понедельник'],
            ['week_num_day' => 2, 'ua_name' => 'Вівторок',   'en_name' => 'Tuesday', 'ru_name' => 'Вторник'],
            ['week_num_day' => 3, 'ua_name' => 'Середа',     'en_name' => 'Wednesday', 'ru_name' => 'Среда'],
            ['week_num_day' => 4, 'ua_name' => 'Четвер',     'en_name' => 'Thursday', 'ru_name' => 'Четверг'],
            ['week_num_day' => 5, 'ua_name' => 'П’ятниця',   'en_name' => 'Friday', 'ru_name' => 'Пятница'],
            ['week_num_day' => 6, 'ua_name' => 'Субота',     'en_name' => 'Saturday', 'ru_name' => 'Суббота'],
            ['week_num_day' => 7, 'ua_name' => 'Неділя',     'en_name' => 'Sunday', 'ru_name' => 'Воскресенье'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('apteks_weekdays_locale');
    }
};
