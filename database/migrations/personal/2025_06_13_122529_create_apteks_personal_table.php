<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('apteks_personal', function (Blueprint $table) {
            $table->id();
            $table->string('_IDTochka', 9); // Код аптеки
            $table->unsignedBigInteger('apteka_id')->nullable();
            $table->string('apteka', 150);  // Назва аптеки
            $table->string('fio', 100);     // ПІБ
            $table->string('position', 100); // Посада
            $table->string('phone', 103)->nullable(); // Телефон
            $table->integer('sort')->nullable(); // Сортування
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });

        Schema::table('apteks_personal', function (Blueprint $table) {
            $table->index(['_IDTochka', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apteks_personal');
    }
};