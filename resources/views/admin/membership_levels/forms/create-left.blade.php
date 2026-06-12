<div class="col-12 col-md-9">
    <div class="card mb-3">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Thông tin hạng thành viên') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Tên hạng thành viên') }}" icon="ti ti-diamond" />
                    <x-input name="name" :value="$instance->name ?? old('name')" placeholder="{{ __('Tên hạng thành viên') }}" />
                </div>
            </div>
            <div class="col-md-3 col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Số điểm thăng hạng') }}" icon="ti ti-coins" />
                    <x-input name="min_points" :value="$instance->min_points ?? old('min_points')" placeholder="{{ __('Số điểm để được thăng hạng') }}" />
                </div>
            </div>
            <div class="col-md-3 col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Phần trăm giảm giá') }}" icon="ti ti-coins" />
                    <x-input name="discount_percentage" :value="$instance->discount_percentage ?? old('discount_percentage')"
                        placeholder="{{ __('Phần trăm giảm giá') }}" />
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Màu sắc 1') }}" icon="ti ti-box" />
                    <x-input-color name="color_1" :value="$instance->color_1 ?? old('color_1')" />
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Màu sắc 2') }}" icon="ti ti-box" />
                    <x-input-color name="color_2" :value="$instance->color_2 ?? old('color_2')" />
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Màu sắc 3') }}" icon="ti ti-box" />
                    <x-input-color name="color_3" :value="$instance->color_3 ?? old('color_3')" />
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Icon') }}" icon="ti ti-box" />
                    <x-select name="icon" id="icon-select" class="select2-bs5"
                        data-ajax-url="{{ route('admin.search.select.icon') }}" data-ajax-cache="true"
                        :required="true">
                        @if (isset($instance->icon))
                            <x-select-option :option="$instance->icon" :value="$instance->icon" :title="$instance->icon" />
                        @endif
                    </x-select>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Mô tả') }}" icon="ti ti-file-description" />
                    <textarea name="description" class="ckeditor visually-hidden">{{ $instance->description ?? old('description') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
