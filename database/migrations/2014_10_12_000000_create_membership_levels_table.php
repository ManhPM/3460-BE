<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('membership_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Tên hạng (VD: Silver, Gold, Platinum)
            $table->integer('min_points')->default(0); // Số điểm xét hạng tối thiểu để đạt hạng
            $table->integer('discount_percentage')->default(0); // % giảm giá cho hạng này
            $table->char('color_1');
            $table->char('color_1');
            $table->char('icon');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_levels');
    }
};
