<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <x-button.submit :title="__('Cập nhật')" />
            <x-button.modal-delete data-route="{{ route('admin.product.delete', $product->id) }}" :title="__('Xóa')" />
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
                    <x-input-checkbox :depth="$category->depth" :checked="$product->categories->pluck('id')->toArray()" name="categories_id[]" :label="$category->name"
                        :value="$category->id" />
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
            <x-input-switch name="product[is_contact_price]" value="1" :label="__('Giá liên hệ?')" :checked="$product->is_contact_price == 1" />
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Đang hoạt động') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="product[is_active]" value="0">
            <x-input-switch name="product[is_active]" value="1" :label="__('Đang hoạt động?')" :checked="$product->is_active == 1" />
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
                <x-select-option :option="$product->is_featured ?: '2'" value="2" :title="__('Không')" />
            </x-select>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('Ảnh đại diện') }}</span>
        </div>
        <div class="card-body p-0">
            <x-input-image-ckfinder name="product[avatar]" showImage="avatar" :value="$product->avatar" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('Thư viện ảnh') }}</span>
        </div>
        <div class="card-body p-0">
            <x-input-gallery-ckfinder name="product[gallery]" type="multiple" :value="$product->gallery" />
        </div>
    </div>
</div>
