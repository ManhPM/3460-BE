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
        Schema::table('voucher_programs', function (Blueprint $table) {
            // Thêm trường expiration_days
            $table->integer('expiration_days')->nullable()->after('qty');
        });

        // Xóa các trường date_start, date_end, date_expired trong một Schema::table riêng
        Schema::table('voucher_programs', function (Blueprint $table) {
            $table->dropColumn(['date_start', 'date_end', 'date_expired']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher_programs', function (Blueprint $table) {
            // Thêm lại các trường cũ
            $table->date('date_start')->after('avatar');
            $table->date('date_end')->after('date_start');
            $table->date('date_expired')->after('date_end');

            // Xóa trường expiration_days
            $table->dropColumn('expiration_days');
        });
    }
};
