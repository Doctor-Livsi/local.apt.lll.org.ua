<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apteks_connections_count', function (Blueprint $table) {
            $table->id(); // Автоінкрементний PRIMARY KEY
            $table->integer('total')->nullable(); // Загальна кількість аптек
            $table->integer('total_apteks')->nullable(); // Загальна кількість аптек
            $table->integer('connected_apteks')->nullable(); // Кількість аптек на зв’язку
            $table->integer('disconnected_total')->nullable(); // Кількість аптек без зв’язку
            $table->integer('disconnected_min')->nullable();
            $table->integer('disconnected_lvl1')->nullable();
            $table->integer('disconnected_lvl2')->nullable();
            $table->integer('disconnected_lvl3')->nullable();
            $table->integer('disconnected_max')->nullable(); // Не на зв’язку > 24 год
            $table->timestamp('created_at')->useCurrent(); // Дата створення
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apteks_connections_count');
    }
};