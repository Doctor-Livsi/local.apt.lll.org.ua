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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_patronymic')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_employee')->default(false);
            $table->foreignId('department_id')->nullable()->constrained('users_department')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('users_position')->nullOnDelete();
            $table->boolean('is_admin')->default(false);
            $table->softDeletes();
            $table->string('photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем внешние ключи
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            // Удаляем поля
            $table->dropColumn([
                'first_name',
                'last_name',
                'first_patronymic',
                'birth_date',
                'is_employee',
                'department_id',
                'position_id',
                'is_admin',
                'deleted_at',
                'photo',
            ]);
        });
    }
};
