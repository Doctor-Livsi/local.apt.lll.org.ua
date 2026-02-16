<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apteks_employees', function (Blueprint $table) {

            $table->id();

            // Код аптеки з 1С (рядок)
            $table->string('id_tochka', 9);

            // ID аптеки (int, не nullable)
            $table->integer('apteka_id');

            // Назва аптеки (для швидкого UI без JOIN)
            $table->string('apteka_name', 256)->nullable();

            // Унікальний ідентифікатор співробітника з 1С
            $table->binary('person_rref', 16);

            // ПІБ (укр / рос)
            $table->string('fio_ua', 256)->nullable();
            $table->string('fio_ru', 256)->nullable();

            // Код співробітника 1с-зуп
            $table->integer('code')->nullable();
            // Посада
            $table->string('position', 128)->nullable();

            // Телефон (агрегований рядок)
            $table->string('phone', 128)->nullable();

            // Сортування
            $table->integer('sort')->nullable();

            // Чи співробітник зараз на роботі
            $table->boolean('on_working')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Індекси
            $table->index(['apteka_id', 'sort']);
            $table->index(['person_rref']);
            $table->unique(['apteka_id', 'person_rref'], 'uq_apteka_employee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apteks_employees');
    }
};
