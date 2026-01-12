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
            $table->boolean('is_super_admin')->default(false)->after('is_employee');
            $table->boolean('is_user')->default(true)->after('is_super_admin');
        });
        // Встановлюємо is_user = true для всіх існуючих користувачів
        \App\Models\User::query()->update(['is_user' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
            $table->dropColumn('is_user');
        });
    }
};
