<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->double('price')->nullable();
            $table->double('flashsale_price')->nullable();
            $table->double('promotion_price')->nullable();
            $table->text('image')->nullable();
            $table->integer('qty');
            $table->tinyInteger('is_active')->default(1);
            $table->text('desc')->nullable();
            $table->tinyInteger('position')->default(0);
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_variations', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('products_variations');
    }
};
