<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <x-button.submit :title="__('Cập nhật')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-category"></i>
            <span class="ms-2">{{ __('Danh mục nằm trong section') }}</span>
        </div>
        <div class="card-body wrap-list-checkbox p-2">
            @foreach ($categories as $category)
                <x-input-checkbox :checked="$section->categories->pluck('id')->toArray()" :depth="$category->depth" name="categories_id[]" :label="$category->name"
                    :value="$category->id" />
            @endforeach
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('Ảnh trưng bày') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="avatar" :value="$section->avatar" />
        </div>
    </div>
</div>
