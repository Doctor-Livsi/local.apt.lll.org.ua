<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::connection('sqlsrv')->create('posts', function (Blueprint $table) {
        $table->id(); // автоінкрементне поле ID
        $table->string('title');
        $table->text('body')->nullable();
        $table->timestamps(); // created_at + updated_at
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
