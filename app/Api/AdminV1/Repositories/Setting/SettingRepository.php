<?php

namespace App\Api\AdminV1\Repositories\Setting;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Setting;
use App\Enums\Setting\SettingGroup;

class SettingRepository extends EloquentRepository implements SettingRepositoryInterface
{
    public function getModel(): string
    {
        return Setting::class;
    }

    public function all()
    {
        return $this->model->all()->pluck('plain_value', 'setting_key')->toArray();
    }

    public function getAllWithDetails()
    {
        // Get valid group values from SettingGroup enum
        $validGroups = [
            SettingGroup::General,
            SettingGroup::Config,
            SettingGroup::Slider,
            SettingGroup::Membership,
        ];

        return $this->model->select('setting_key', 'setting_name', 'plain_value', 'group', 'type_input')
            ->whereIn('group', $validGroups)
            ->orderBy('group')
            ->orderBy('setting_name')
            ->get()
            ->map(function ($setting) {
                return [
                    'key' => $setting->setting_key,
                    'name' => $setting->setting_name,
                    'value' => $setting->plain_value ?? '',
                    'group' => $setting->group?->value ?? $setting->group,
                    'group_name' => $setting->group?->description ?? '',
                    'type_input' => $setting->type_input?->value ?? $setting->type_input,
                    'type_input_name' => $setting->type_input?->description ?? '',
                ];
            });
    }

    public function getByKey(string $key)
    {
        return $this->model->where('setting_key', $key)->first();
    }

    public function updateByKey(string $key, string $value)
    {
        return $this->model->updateOrCreate(
            ['setting_key' => $key],
            ['plain_value' => $value]
        );
    }

    public function updateMultiple(array $settings)
    {
        foreach ($settings as $setting) {
            $key = $setting['key'] ?? $setting['setting_key'] ?? null;
            $value = $setting['value'] ?? $setting['plain_value'] ?? '';
            if ($key) {
                $this->updateByKey($key, $value);
            }
        }

        // Clear cache if exists
        if (defined('Setting::CACHE_KEY_GET_ALL')) {
            \Cache::forget(Setting::CACHE_KEY_GET_ALL);
        }

        return $this->all();
    }
}
