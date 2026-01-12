<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apteks_brand', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name', 100)->nullable(); // NVARCHAR(100)
            $table->string('logo', 100)->nullable(); // NVARCHAR(100)
            $table->string('icon', 100)->nullable(); // NVARCHAR(100)
            $table->timestamps(); // created_at, updated_at
        });

        // Додаємо зв’язок із apteks
        Schema::table('apteks', function (Blueprint $table) {
            $table->foreign('brand_id')->references('id')->on('apteks_brand')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('apteks', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
        });
        Schema::dropIfExists('apteks_brand');
    }
};