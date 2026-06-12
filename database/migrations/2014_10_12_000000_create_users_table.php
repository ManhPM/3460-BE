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
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('code', 50);
            $table->string('slug');
            $table->string('fullname');
            $table->char('email', 100)->nullable();
            $table->char('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->text('avatar')->nullable();
            $table->text('affiliate_code')->nullable();
            $table->text('referrer_code')->nullable();
            $table->text('bank_account')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->integer('points')->default(0);
            $table->integer('membership_level_points')->default(0);
            $table->integer('commission')->default(0);
            $table->text('verify_code')->nullable();
            $table->date('birthday')->nullable();
            $table->string('device_token')->nullable();
            $table->tinyInteger('gender');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('verify_code_expiration')->nullable();
            $table->string('token_get_password')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_email_verified')->default(0);
            $table->boolean('is_phone_verified')->default(0);

            $table->unsignedBigInteger('membership_id');
            $table->foreign('membership_id')->references('id')->on('membership_levels')->onDelete('set null');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
