<?php

namespace App\Admin\Services\Discount;

use  App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class DiscountService implements DiscountServiceInterface
{

    protected array $data;

    protected DiscountRepositoryInterface $repository;

    public function __construct(
        DiscountRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }


    public function update(Request $request): object|bool
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $discountId = $this->data['id'];
            $instance = $this->repository->update($discountId, $this->data);
            DB::commit();
            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Discount update failed:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }


    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }



    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $instance = $this->repository->create($this->data);
            DB::commit();

            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to create discount:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
