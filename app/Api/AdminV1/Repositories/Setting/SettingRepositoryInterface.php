<?php

namespace App\Api\AdminV1\Repositories\Setting;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface SettingRepositoryInterface extends EloquentRepositoryInterface
{
    public function all();
    public function getAllWithDetails();
    public function getByKey(string $key);
    public function updateByKey(string $key, string $value);
    public function updateMultiple(array $settings);
}

