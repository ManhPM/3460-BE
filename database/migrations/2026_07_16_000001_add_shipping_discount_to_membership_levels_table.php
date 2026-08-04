<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_discount_amount')->default(0)->after('discount_percentage')
                ->comment('Số tiền giảm phí vận chuyển (VD: 10000 = giảm 10.000đ)');
        });
    }

    public function down()
    {
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->dropColumn('shipping_discount_amount');
        });
    }
};
