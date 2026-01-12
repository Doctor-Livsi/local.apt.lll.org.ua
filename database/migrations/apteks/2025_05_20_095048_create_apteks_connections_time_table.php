<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apteks_connections_time', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary(); // PRIMARY KEY, без автоінкременту
            $table->dateTime('loss_of_server')->nullable()->index(); // DATETIME, індекс
            $table->dateTime('loss_of_services')->nullable()->index(); // DATETIME, індекс
            $table->dateTime('loss_of_routers')->nullable()->index(); // DATETIME, індекс
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apteks_connections_time');
    }
};
