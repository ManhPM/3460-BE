<div class="wrap-item-product-variation ui-sortable-handle bg-white">
    <div class="d-flex justify-content-between align-items-center border-bottom shadow-sm">
        <div class="wrap-select-attribute-for-variation d-flex flex-fill gap-2 p-2">
            @foreach ($attribute_variations as $keyParent => $attributeVariation)
                <x-select
                    name="products_variations[attribute_variation_id][{{ $identity ?? ($productVariation->id ?? '') }}][]">
                    @foreach ($attributeVariation as $key => $value)
                        <x-select-option :option="$selected[$keyParent] ?? ($productVariation->attributeVariations ?? '')" :value="$key" :title="$value" />
                    @endforeach
                </x-select>
            @endforeach
        </div>
        <div class="wrap-action d-flex justify-content-end align-items-center flex-fill gap-2 p-2"
            data-bs-toggle="collapse" data-bs-target="#collapseVariation{{ $identity ?? ($productVariation->id ?? '') }}"
            aria-expanded="false" aria-controls="collapseVariation{{ $identity ?? ($productVariation->id ?? '') }}">
            <span class="cursor-move"><i class="ti ti-menu-order"></i></span>
            <x-button type="button" class="badge badge-outline text-red remove-product-variation-item"
                :data-product-variaton-delete-route="isset($productVariation)
                    ? route('admin.product.variation.delete', $productVariation->id)
                    : 0">
                {{ __('Xóa') }}
            </x-button>
        </div>
    </div>
    <div class="collapse" id="collapseVariation{{ $identity ?? ($productVariation->id ?? '') }}">
        <x-input type="hidden" class="input-product-attribute-id"
            name="products_variations[id][{{ $identity ?? ($productVariation->id ?? '') }}]" :value="$productVariation->id ?? 0" />
        <div class="row g-0 bg-light p-3">
            <div class="col-12 mb-3">
                <x-label text="{{ __('Hình ảnh') }}" icon="ti ti-photo" />
                <div class="d-flex justify-content-center">
                    <x-input-image-ckfinder small="true"
                        name="products_variations[image][{{ $identity ?? ($productVariation->id ?? '') }}]"
                        showImage="productVariationImage{{ $identity ?? ($productVariation->id ?? '') }}"
                        :value="$productVariation->image ?? ''" />
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <x-label text="{{ __('Giá bán thường') }}" icon="ti ti-cash" required="true" />
                    <x-input id="inputPVP{{ $identity ?? ($productVariation->id ?? '') }}"
                        name="products_variations[price][{{ $identity ?? ($productVariation->id ?? '') }}]"
                        :value="$productVariation->price ?? ''" :placeholder="__('Giá bán thường')" :required="true" data-parsley-type="number"
                        data-parsley-type-message="{{ __('Trường này phải là số.') }}" />
                </div>
                <div class="col-6">
                    <x-label text="{{ __('Giá khuyến mãi') }}" icon="ti ti-discount-2" />
                    <x-input id="inputPMP{{ $identity ?? ($productVariation->id ?? '') }}"
                        name="products_variations[promotion_price][{{ $identity ?? ($productVariation->id ?? '') }}]"
                        :value="$productVariation->promotion_price ?? ''" :placeholder="__('Giá khuyến mãi')" data-parsley-type="number"
                        data-parsley-lt="#inputPVP{{ $identity ?? ($productVariation->id ?? '') }}"
                        data-parsley-number-message="Trường này phải là số."
                        data-parsley-lt-message="Giá khuyến mãi phải nhỏ hơn giá mặc định." />
                </div>
            </div>
        </div>
    </div>
</div>
