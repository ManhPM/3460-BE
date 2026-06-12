<?php

namespace App\Admin\Services\ShippingRate;

use  App\Admin\Repositories\ShippingRate\ShippingRateRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ShippingRateService implements ShippingRateServiceInterface
{

    protected array $data;

    protected ShippingRateRepositoryInterface $repository;

    public function __construct(
        ShippingRateRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }


    public function update(Request $request): object|bool
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $instance = $this->repository->update($this->data['id'], $this->data);
            DB::commit();
            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to update shipping rate:', [
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
            Log::error('Failed to create shipping rate:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
