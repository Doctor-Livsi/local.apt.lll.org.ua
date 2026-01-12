<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id(); // автоінкремент, primary key
            $table->string('email'); // email користувача
            $table->string('ip_address'); // IP-адреса
            $table->text('user_agent'); // інформація про браузер
            $table->string('method'); // метод входу (web/api)
            $table->boolean('success'); // чи була спроба успішною
            $table->timestamp('created_at')->useCurrent(); // час спроби

            // Індекси для швидкого пошуку
            $table->index('email'); // для пошуку за email
            $table->index('ip_address'); // для пошуку за IP
            $table->index('created_at'); // для пошуку за часом
            $table->index(['ip_address', 'created_at']); // для аналізу спроб за IP та часом
            $table->index(['email', 'ip_address']); // для перевірки різних IP для одного email
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};