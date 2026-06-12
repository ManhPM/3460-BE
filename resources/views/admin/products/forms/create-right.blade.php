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
            <input type="text" class="form-control mb-2" placeholder="Tìm kiếm danh mục..."
                onkeyup="filterCategories(this)">
            <div id="category-checkbox-list">
                @foreach ($categories as $category)
                    <x-input-checkbox :depth="$category->depth" name="categories_id[]" :label="$category->name" :value="$category->id" />
                @endforeach
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Giá liên hệ') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="product[is_contact_price]" value="0">
            <x-input-switch name="product[is_contact_price]" value="1" :label="__('Giá liên hệ?')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-star"></i>
            <span class="ms-2">{{ __('Nổi bật') }}</span>
        </div>
        <div class="card-body p-2">
            <x-select class="form-select" name="product[is_featured]" :required="true">
                <x-select-option value="1" :title="__('Có')" />
                <x-select-option value="2" :title="__('Không')" />
            </x-select>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('Ảnh đại diện') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="product[avatar]" showImage="avatar" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('Thư viện ảnh') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-gallery-ckfinder name="product[gallery]" type="multiple" />
        </div>
    </div>
</div>
