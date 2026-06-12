<?php

use App\Enums\Discount\DiscountValueType;
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
        Schema::create('discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('code', 255);
            $table->timestamp('date_start');
            $table->timestamp('date_end');
            $table->integer('max_usage');
            $table->integer('max_usage_per_user')->default(1);
            $table->double('min_order_amount');
            $table->double('max_discount_value');
            $table->double('discount_value');
            $table->tinyInteger('type')->default(DiscountValueType::Money->value);
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
        Schema::dropIfExists('discounts');
    }
};
