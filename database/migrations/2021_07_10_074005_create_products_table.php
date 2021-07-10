<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->unsigned()->nullable()->default(NULL);
            $table->string('name', 255);
            $table->integer('category_id')->unsigned()->nullable()->default(NULL);
            $table->integer('price')->unsigned()->nullable()->default(0);
            $table->integer('stock')->unsigned()->nullable()->default(0);
            $table->text('description')->nullable();

            $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
