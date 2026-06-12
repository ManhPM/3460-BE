<div id="attributes" class="tab-pane" role="tabpanel" aria-labelledby="tabAttributes">
    <div class="row g-0 wrap-select-attribute border-bottom gap-2 p-3">
        <div class="col">
            <x-select id="selectAttribute" name="attributes">
                <x-select-option value="" :title="__('Chọn thuộc tính sản phẩm')" />
                @foreach ($attributes as $key => $value)
                    <x-select-option :value="$key" :disabled="isset($product->arrProductAttributesId)
                        ? in_array($key, $product->arrProductAttributesId)
                        : false" :title="$value" />
                @endforeach
            </x-select>
        </div>
        <div class="col-auto">
            <button type="button" id="btnAttributeAddNew" class="btn btn-outline-primary">{{ __('Thêm') }}</button>
        </div>
    </div>
    <div id="listAttribute" class="wrap-attribute d-flex flex-column reorder-list gap-2">
        @isset($product)
            @foreach ($product->productAttributes as $productAttribute)
                @include('admin.products.data.partials.product-arttibutes', [
                    'attribute' => $productAttribute->attribute,
                    'productAttributeId' => $productAttribute->id,
                    'attribute_variations' => $productAttribute->attributeVariations,
                ])
            @endforeach
        @endisset
    </div>
    <div class="p-3">
        <x-button type="button" id="btnSaveAttribute" class="btn btn-default-cms">{{ __('Lưu thuộc tính') }}</x-button>
    </div>
</div>
