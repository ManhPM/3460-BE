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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_variation_id')->nullable();

            $table->double('unit_price');
            $table->tinyInteger('is_reviewed');
            $table->text('affiliate_code')->nullable();
            $table->text('affiliate_earning')->nullable();
            $table->text('product_name');
            $table->text('product_avatar');
            $table->text('product_slug');
            $table->integer('qty');
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('product_variation_id')->references('id')->on('product_variations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['order_id']);
        });
        Schema::dropIfExists('order_details');
    }
};
