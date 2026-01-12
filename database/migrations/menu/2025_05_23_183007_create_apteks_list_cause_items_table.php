<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApteksListCauseItemsTable extends Migration
{
    public function up()
    {
        Schema::create('apteks_list_cause_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('type', 20)->nullable();
            $table->string('link', 255)->nullable();
            $table->integer('page_id')->unsigned()->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('lft')->unsigned()->nullable();
            $table->integer('rgt')->unsigned()->nullable();
            $table->integer('depth')->unsigned()->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apteks_list_cause_items');
    }
}
