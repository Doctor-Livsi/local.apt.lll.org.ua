<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('apteks_disconnected', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('apteka_id');
            $table->dateTime('disconnected_at');
            $table->dateTime('reconnected_at')->nullable();
            $table->unsignedInteger('disconnected')->nullable();

            // тимчасово пропускаємо поле offline

            $table->unsignedInteger('cause_id')->nullable();

            $table->dateTime('created_at')->default(DB::raw('GETDATE()'));
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('cause_id')
                ->references('id')
                ->on('apteks_list_cause_items')
                ->nullOnDelete();

            $table->index(['apteka_id']);
            $table->index(['disconnected_at']);
            $table->index(['reconnected_at']);
            $table->index(['deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apteks_disconnected');
    }
};
