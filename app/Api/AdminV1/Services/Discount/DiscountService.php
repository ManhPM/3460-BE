<?php

namespace App\Api\AdminV1\Services\Discount;

use App\Api\AdminV1\Repositories\Discount\DiscountRepositoryInterface;
use Carbon\Carbon;

class DiscountService
{
    protected $repository;

    public function __construct(DiscountRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Convert datetime from ISO format to MySQL datetime format
     */
    protected function formatDateTime($dateTime)
    {
        if (empty($dateTime)) {
            return null;
        }

        try {
            // Try to parse ISO format or any datetime format
            $carbon = Carbon::parse($dateTime);
            // Return MySQL datetime format: Y-m-d H:i:s
            return $carbon->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // If parsing fails, return as is
            return $dateTime;
        }
    }

    public function create(array $data)
    {
        // Format datetime fields for MySQL
        if (isset($data['date_start'])) {
            $data['date_start'] = $this->formatDateTime($data['date_start']);
        }
        if (isset($data['date_end'])) {
            $data['date_end'] = $this->formatDateTime($data['date_end']);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        // Format datetime fields for MySQL
        if (isset($data['date_start'])) {
            $data['date_start'] = $this->formatDateTime($data['date_start']);
        }
        if (isset($data['date_end'])) {
            $data['date_end'] = $this->formatDateTime($data['date_end']);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}

