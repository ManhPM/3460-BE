<?php

use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
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
        Schema::create('voucher_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar');
            $table->date('date_start');
            $table->date('date_end');
            $table->date('date_expired');
            $table->integer('qty');
            $table->double('min_order_amount')->nullable();
            $table->double('max_discount_value')->nullable();
            $table->double('discount_value');
            $table->tinyInteger('type')->default(DiscountValueType::Money->value); // 0: Money, 1: Percentage
            $table->tinyInteger('voucher_type')->default(VoucherType::Product->value); // 0: Product, 1: Shipping, etc.
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
        Schema::dropIfExists('voucher_programs');
    }
};
