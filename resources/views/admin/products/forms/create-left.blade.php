<div class="col-12 col-md-9">
    <div class="row">
        <!-- name -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="ti ti-brand-producthunt"></i>
                    <span class="ms-2">{{ __('Thông tin sản phẩm') }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <x-label for="product[name]" text="{{ __('Tên sản phẩm') }}" icon="ti ti-brand-producthunt"
                            required="true" />
                        <x-input name="product[name]" :value="old('product.name')" :required="true"
                            placeholder="{{ __('Tên sản phẩm') }}" />
                    </div>

                    <div class="mb-3">
                        <x-label for="product[desc]" text="{{ __('Mô tả') }}" icon="ti ti-file-description" />
                        <textarea name="product[desc]" class="ckeditor visually-hidden">{{ old('product.desc') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- data -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body p-0">
                    @include('admin.products.data.data-product')
                </div>
            </div>
        </div>
    </div>
</div>
