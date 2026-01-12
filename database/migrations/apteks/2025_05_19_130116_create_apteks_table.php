<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apteks', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary(); // Ручний id аптеки
            $table->string('name', 255)->nullable()->index();
            $table->string('name_full', 255)->nullable()->index();
            $table->string('license', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('phone', 255)->nullable()->index();
            $table->string('apteka_ip', 32)->nullable()->index();
            $table->string('router_ip', 15)->nullable();
            $table->string('router_serial_number', 50)->nullable();
            $table->string('address_full', 255)->nullable()->index();
            $table->string('address_region', 100)->nullable()->index();
            $table->string('address_town', 100)->nullable()->index();
            $table->string('address_type', 100)->nullable()->index();
            $table->string('address_street', 100)->nullable()->index();
            $table->string('address_house_number', 16)->nullable()->index();
            $table->decimal('google_x', 12, 6)->nullable();
            $table->decimal('google_y', 12, 6)->nullable();
            $table->integer('firma_id')->nullable();
            $table->string('firma', 100)->nullable();
            $table->unsignedInteger('brand_id')->nullable();
            $table->string('brand', 100)->nullable();
            $table->integer('cause_id')->nullable();
            $table->boolean('closed')->default(false)->index();
            $table->date('open_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Індекси для швидшого пошуку
            $table->index('firma_id');
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apteks');
    }
};