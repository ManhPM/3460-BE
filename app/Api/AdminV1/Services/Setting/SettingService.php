<?php

namespace App\Api\AdminV1\Services\Setting;

use App\Api\AdminV1\Repositories\Setting\SettingRepositoryInterface;

class SettingService
{
    protected $repository;

    public function __construct(SettingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function update(array $data)
    {
        return $this->repository->updateMultiple($data['settings']);
    }
}

