<?php

use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'wallet_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->double('wallet_balance')->default(0)->after('commission');
            });
        }

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->double('amount');
            $table->enum('type', WalletTransactionType::getValues())->default(WalletTransactionType::Deposit->value); // deposit, withdraw, payment, refund
            $table->enum('status', WalletTransactionStatus::getValues())->default(WalletTransactionStatus::Pending->value); // pending, approved, rejected
            $table->text('note')->nullable();
            $table->string('proof_image')->nullable()->after('note');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'wallet_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('wallet_balance');
            });
        }

        Schema::dropIfExists('wallet_transactions');
    }
};
