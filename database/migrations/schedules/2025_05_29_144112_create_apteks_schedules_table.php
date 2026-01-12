<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApteksSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apteks_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apteka_id'); // ID аптеки
            $table->unsignedTinyInteger('week_num_day');// 1–7, без foreign key
            $table->integer('week_number'); // Номер тижня
            $table->integer('year'); // Рік
            $table->string('schedules'); // Назва графіка (наприклад: "08:00 - 20:00", "Працює", "Цілодобово")
            $table->time('start_at')->nullable(); // Початок робочого часу
            $table->time('end_at')->nullable(); // Кінець робочого часу
            $table->date('date_at'); // Дата
            $table->timestamps();

            // 🔐 Унікальна комбінація
            $table->unique(['apteka_id', 'date_at']);

            // 🔍 Індекси для фільтрації
            $table->index('apteka_id');
            $table->index('date_at');
            $table->index('year');
            $table->index('schedules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apteks_schedules');
    }
}
