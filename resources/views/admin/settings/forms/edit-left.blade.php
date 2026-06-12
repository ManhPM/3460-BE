<div class="card h-100">
    <div class="card-header justify-content-center">
        <h2 class="mb-0">{{ $title ?? __('Thông tin cài đặt') }}</h2>
    </div>
    <div class="row card-body wrap-loop-input">
        @foreach ($settings as $setting)
            @if (!env('IS_PRO'))
                @if (!in_array($setting->setting_key, ['commission_rate']))
                    <div class="{{ $setting->class ?? 'col-12' }}">
                        <div class="mb-3">
                            @if (in_array($setting->setting_key, ['is_object_valid', 'is_testing_zalostore', 'is_returnable']))
                                <x-label class="mb-1" text="{{ $setting->setting_name }}" :icon="$setting->icon" />
                                <input type="hidden" name="{{ $setting->setting_key }}" value="0">
                                <x-input-switch name="{{ $setting->setting_key }}" value="1" :label="__('')"
                                    :checked="$setting->plain_value == 1" />
                            @else
                                <x-label class="mb-1" text="{{ $setting->setting_name }}" :icon="$setting->icon" />
                                <x-dynamic-component :component="$setting->getNameComponentTypeInput()" :name="$setting->setting_key" :value="$setting->plain_value"
                                    showImage="{{ $setting->setting_key }}" :required="true" />
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="{{ $setting->class ?? 'col-12' }}">
                    <div class="mb-3">
                        @if (in_array($setting->setting_key, ['is_object_valid', 'is_testing_zalostore', 'is_returnable']))
                            <x-label class="mb-1" text="{{ $setting->setting_name }}" :icon="$setting->icon" />
                            <input type="hidden" name="{{ $setting->setting_key }}" value="0">
                            <x-input-switch name="{{ $setting->setting_key }}" value="1" :label="__('')"
                                :checked="$setting->plain_value == 1" />
                        @else
                            <x-label class="mb-1" text="{{ $setting->setting_name }}" :icon="$setting->icon" />
                            <x-dynamic-component :component="$setting->getNameComponentTypeInput()" :name="$setting->setting_key" :value="$setting->plain_value"
                                showImage="{{ $setting->setting_key }}" :required="true" />
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
