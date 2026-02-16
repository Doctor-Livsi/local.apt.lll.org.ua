<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chatbot_stats_counts', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Статистика (цілі числа)
            $table->integer('queue')->default(0);      // Завдань у черзі
            $table->integer('in_work')->default(0);    // Завдань у роботі
            $table->integer('total')->default(0);      // Всього завдань
            $table->integer('employees')->default(0);  // Співробітників

            // Час фіксації стану (для відображення)
            $table->dateTime('created_at')->useCurrent();

            // Soft-delete (на майбутнє)
            $table->dateTime('deleted_at')->nullable();

            // Для швидкого читання останнього актуального рядка
            $table->index(['deleted_at', 'id'], 'ix_chatbot_stats_counts_active_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_stats_counts');
    }
};
