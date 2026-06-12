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
            <i class="ti ti-category"></i>
            <span class="ms-2">{{ __('Danh mục') }}</span>
        </div>
        <div class="card-body wrap-list-checkbox p-2">
            @foreach ($categories as $category)
                <x-input-checkbox :depth="$category->depth" name="categories_id[]" :label="$category->name" :value="$category->id" />
            @endforeach
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-star"></i>
            <span class="ms-2">{{ __('Nổi bật') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_featured" value="0">
            <x-input-switch name="is_featured" value="1" :label="__('Nổi bật?')" />
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
                    <x-select-option :value="$key" :title="$value" />
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
            <x-input-image-ckfinder name="avatar" showImage="avatar" :value="old('avatar')" />
        </div>
    </div>
</div>
