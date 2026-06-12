<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body p-2">
            <x-button.submit :title="__('Thêm')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Hiện ở trang chủ mobile') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_home" value="0">
            <x-input-switch name="is_home" value="1" :label="__('Hiện ở trang chủ mobile?')" :checked="isset($category) ? $category->is_home == 1 : ''" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-toggle-right"></i>
            <span class="ms-2">{{ __('Trạng thái') }}</span>
        </div>
        <div class="card-body p-2">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="__($value)" />
                @endforeach
            </x-select>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('Ảnh đại diện') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="avatar" />
        </div>
    </div>
</div>
