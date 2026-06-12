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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('code', 255)->unique();
            $table->timestamp('date_end');
            $table->double('min_order_amount');
            $table->double('max_discount_value');
            $table->double('discount_value');
            $table->text('avatar');
            $table->tinyInteger('is_used')->default(0);
            $table->tinyInteger('type')->default(DiscountValueType::Money->value);
            $table->tinyInteger('voucher_type')->default(VoucherType::Product->value);
            $table->timestamps();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};
