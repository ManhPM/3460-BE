<ul id="tabDataProduct" class="nav nav-pills flex-column" role="tablist">
    <li class="nav-item type-simple">
        <button type="button" id="tabPrice" class="nav-link text-black gap-2 align-items-center active w-100"
            data-bs-toggle="tab" data-bs-target="#price" role="tab" aria-controls="price" aria-selected="true">
            <i class="ti ti-premium-rights"></i> {{ __('Giá sản phẩm') }}
        </button>
    </li>
    <li class="nav-item type-variable" style="display: none;">
        <button type="button" id="tabAttributes" class="nav-link text-black align-items-center gap-2 w-100"
            data-bs-toggle="tab" data-bs-target="#attributes" aria-controls="attributes" aria-selected="false">
            <i class="ti ti-clipboard-list"></i> {{ __('Các thuộc tính') }}
        </button>
    </li>
    <li class="nav-item type-variable" style="display: none;">
        <button type="button" id="tabVariations" class="nav-link text-black align-items-center gap-2 w-100"
            data-bs-toggle="tab" data-bs-target="#variations" aria-controls="variations" aria-selected="false">
            <i class="ti ti-border-all"></i> {{ __('Các biến thể') }}
        </button>
    </li>
</ul>
