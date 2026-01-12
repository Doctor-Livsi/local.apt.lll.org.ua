<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'ip' або 'email'
            $table->string('value'); // значення (IP або email)
            $table->timestamp('blocked_until'); // час до якого діє блокування
            $table->timestamp('created_at')->useCurrent();

            // Індекси
            $table->index(['type', 'value']); // для швидкого пошуку блокувань
            $table->index('blocked_until'); // для перевірки активних блокувань
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_logs');
    }
};