<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin danh mục') }}</h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-md-12 col-sm-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên danh mục') }}" icon="ti ti-category" />
                    <x-input name="name" :value="$category->name" :required="true"
                        placeholder="{{ __('Tên danh mục') }}" />
                </div>
            </div>
            <!-- Danh mục cha -->
            <div class="col-md-12 col-sm-12">
                <div class="mb-3">
                    <x-label for="parent_id" text="{{ __('Danh mục cha') }}" icon="ti ti-category" />
                    <x-select class="select2-bs5" name="parent_id">
                        <x-select-option value="" :title="__('Trống')" />
                        @foreach ($categories as $item)
                            <x-select-option :option="$category->parent_id" :value="$item->id" :title="generate_text_depth_tree($item->depth) . ' ' . __($item->name)" />
                        @endforeach
                    </x-select>
                </div>
            </div>
            <!-- position -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <x-label for="position" text="{{ __('Vị trí') }}" icon="ti ti-location" />
                    <x-input type="number" name="position" :value="$category->position" :required="true" />
                </div>
            </div>
            <!-- is active -->
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <x-label for="is_active" text="{{ __('Trạng thái') }}" icon="ti ti-settings-star" />
                    <x-select class="select2-bs5" name="is_active" :required="true">
                        <x-select-option :value="true" :title="__('Hoạt động')" />
                        <x-select-option :option="$category->is_active ?: '0'" value="0" :title="__('Tạm ngưng')" />
                    </x-select>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="mb-3">
                    <x-label for="icon" text="{{ __('Icon') }}" icon="ti ti-favicon" />
                    <i class="{{ $category->icon }} fs-2 ms-1"></i>
                    <x-select name="icon" id="icon-select" class="select2-bs5"
                        data-ajax-url="{{ route('admin.search.select.icon') }}" data-ajax-cache="true"
                        :required="true">
                        <x-select-option :option="$category->icon" :value="$category->icon" :title="$category->icon" />
                    </x-select>
                </div>
            </div>
        </div>
    </div>
</div>
