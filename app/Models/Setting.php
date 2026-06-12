<?php

namespace App\Models;

use App\Enums\Setting\{SettingTypeInput, SettingGroup};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    const CACHE_KEY_GET_ALL = 'cache_settings';

    protected $guarded = [];

    protected $casts = [
        'type_input' => SettingTypeInput::class,
        'group' => SettingGroup::class,
    ];

    public function getNameComponentTypeInput()
    {
        return match ($this->type_input) {
            SettingTypeInput::Text => 'input',
            SettingTypeInput::Number => 'input-number',
            SettingTypeInput::Image => 'input-image-ckfinder',
            SettingTypeInput::Email => 'input-email',
            SettingTypeInput::Phone => 'input-phone',
            SettingTypeInput::Ckeditor => 'input-ckeditor',
            SettingTypeInput::Icon => 'input-icon',
            SettingTypeInput::Color => 'input-color',
            SettingTypeInput::Checkbox => 'input-base-checkbox',
            SettingTypeInput::Video => 'input-video-ckfinder',
            default => 'input',
        };
    }
}
