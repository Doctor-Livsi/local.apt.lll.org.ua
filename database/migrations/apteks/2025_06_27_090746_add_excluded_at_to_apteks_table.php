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
        Schema::table('apteks', function (Blueprint $table) {
            $table->timestamp('excluded_at')->nullable()->after('closed');
        });
    }

    public function down(): void
    {
        Schema::table('apteks', function (Blueprint $table) {
            $table->dropColumn('excluded_at');
        });
    }
};
