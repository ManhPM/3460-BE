<div class="card product-data">
    <div class="card-header d-flex justify-content-start gap-2">
        <span>{{ __('Dữ liệu sản phẩm') }}</span>
        <span>—</span>
        <div class="wrap-select-type">
            @if (isset($product))
                <x-select id="selectProductType" class="form-control" name="product[type]" :required="true">
                    @foreach ($type as $key => $value)
                        <x-select-option :option="$product->type->value" :value="$key" :title="__($value)" />
                    @endforeach
                </x-select>
            @else
                <x-select id="selectProductType" class="form-control" name="product[type]" :required="true">
                    @foreach ($type as $key => $value)
                        <x-select-option :value="$key" :title="__($value)" />
                    @endforeach
                </x-select>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="row g-0">
            <div class="col-12 col-md-3 border-end wrap-nav">
                @include('admin.products.data.tabs.tab-nav')
            </div>
            <div class="col-12 col-md-9 ps-0">
                <div id="tabDataProductContent" class="tab-content">
                    @include('admin.products.data.tabs.tab-price')
                    @include('admin.products.data.tabs.tab-attributes')
                    @include('admin.products.data.tabs.tab-variations')
                </div>
            </div>
        </div>
    </div>
</div>
