<?php

namespace App\Api\V1\Repositories\WalletTransaction;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Traits\AuthService;
use App\Api\V1\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Enums\Transaction\WalletTransactionType;
use App\Models\WalletTransaction;

class WalletTransactionRepository extends EloquentRepository implements WalletTransactionRepositoryInterface
{
    use AuthService;

    public function getModel()
    {
        return WalletTransaction::class;
    }

    public function getByUserAndType($userId, $type = null, $page = 1, $limit = 10, $period = null, $startDate = null, $endDate = null)
    {
        $query = $this->model->where('user_id', $userId);

        // Filter by type
        if ($type && in_array($type, WalletTransactionType::getValues())) {
            $query = $query->where('type', $type);
        }

        // Filter by period
        if ($period) {
            $now = \Carbon\Carbon::now();

            switch ($period) {
                case 'today':
                    $query = $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'this_week':
                    $startOfWeek = $now->copy()->startOfWeek();
                    $endOfWeek = $now->copy()->endOfWeek();
                    $query = $query->whereBetween('created_at', [
                        $startOfWeek->toDateTimeString(),
                        $endOfWeek->toDateTimeString()
                    ]);
                    break;
                case 'this_month':
                    $query = $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                    break;
                case 'this_year':
                    $query = $query->whereYear('created_at', $now->year);
                    break;
                case 'custom':
                    if ($startDate && $endDate) {
                        try {
                            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                            $query = $query->whereBetween('created_at', [
                                $start->toDateTimeString(),
                                $end->toDateTimeString()
                            ]);
                        } catch (\Exception $e) {
                            // Invalid date format, skip custom filter
                        }
                    }
                    break;
            }
        }

        $query = $query->orderBy('created_at', 'desc');

        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
