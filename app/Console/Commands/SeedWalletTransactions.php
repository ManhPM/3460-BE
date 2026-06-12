<?php

namespace App\Console\Commands;

use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedWalletTransactions extends Command
{
    protected $signature = 'wallet:transactions {count=50}';
    protected $description = 'Seed wallet transactions (including all types: deposit, withdraw, payment, refund, affiliate)';

    public function handle(): int
    {
        $count = (int) $this->argument('count');

        if ($count <= 0) {
            $this->error('Count must be greater than 0');
            return Command::FAILURE;
        }

        $this->info("Seeding {$count} wallet transactions...");

        // Get all users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('No users found. Please create users first.');
            return Command::FAILURE;
        }

        // Get all transaction types including affiliate
        $types = [
            WalletTransactionType::Deposit,
            WalletTransactionType::Withdraw,
            WalletTransactionType::Payment,
            WalletTransactionType::Refund,
            WalletTransactionType::Affiliate,
        ];

        $statuses = [
            WalletTransactionStatus::Pending,
            WalletTransactionStatus::Approved,
            WalletTransactionStatus::Rejected,
        ];

        $notes = [
            'Nạp tiền vào ví',
            'Rút tiền từ ví',
            'Thanh toán đơn hàng',
            'Hoàn tiền đơn hàng',
            'Giao dịch ví',
            'Chuyển khoản',
            'Nạp tiền thành công',
            'Rút tiền đang chờ duyệt',
            'Thanh toán online',
            'Hoàn tiền do hủy đơn',
            'Hoa hồng đơn hàng',
            'Hoa hồng cộng tác viên',
            'Tiền hoa hồng',
            'Thanh toán hoa hồng',
        ];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $type = $types[array_rand($types)];
                $status = $statuses[array_rand($statuses)];
                $note = $notes[array_rand($notes)];

                // Generate random amount between 10,000 and 1,000,000
                $amount = rand(10000, 1000000);

                // All amounts are positive, the type determines the transaction direction
                $amount = abs($amount);

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => $type->value,
                    'status' => $status->value,
                    'note' => $note,
                    'order_id' => null, // Can be set to random order ID if needed
                    'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);

                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("Successfully seeded {$count} wallet transactions!");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine();
            $this->error('Error seeding transactions: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
