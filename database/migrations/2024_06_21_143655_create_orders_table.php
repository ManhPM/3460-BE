<?php

use App\Enums\DefaultStatus;
use App\Enums\Order\OrderReview;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('payment_method')->default(PaymentMethod::Direct->value);
            $table->text('note')->nullable();
            $table->text('payment_image')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('fullname')->nullable();
            $table->double('discount_value')->default(0);

            $table->text('voucher_shipping_code')->nullable();
            $table->double('shipping_fee')->default(0);
            $table->double('voucher_shipping_discount_value')->default(0);
            $table->text('voucher_product_code')->nullable();
            $table->double('voucher_product_discount_value')->default(0);

            $table->text('discount_code')->nullable();
            $table->double('total');
            $table->integer('points')->default(0);
            $table->tinyInteger('status')->default(OrderStatus::Pending);
            $table->tinyInteger('payment_status')->default(PaymentStatus::Unpaid->value);
            $table->text('code')->unique();
            $table->tinyInteger('is_deleted')->default(0);
            $table->double('points_discount_value')->default(0);
            $table->double('points_earned')->default(0);
            $table->char('zalo_order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->foreignId('ward_id')->constrained('wards')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
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
        Schema::dropIfExists('orders');
    }
};
