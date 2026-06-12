<?php

use App\Enums\DefaultActiveStatus;
use App\Enums\Product\ProductType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->double('price')->nullable();
            $table->double('flashsale_price')->nullable();
            $table->double('promotion_price')->nullable();
            $table->string('sku')->nullable();
            $table->integer('qty')->nullable();
            $table->tinyInteger('type')->default(ProductType::Simple->value);
            $table->tinyInteger('is_active')->default(1);
            $table->text('avatar')->nullable();
            $table->longText('gallery')->nullable();
            $table->longText('desc')->nullable();
            $table->tinyInteger('is_deleted')->default(0);

            $table->tinyInteger('is_featured')->default(DefaultActiveStatus::Active->value);
            $table->timestamps();
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
};
