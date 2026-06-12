<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin flash sale') }}</h2>
        </div>
        <div class="row card-body">
            <div class="mb-3 col-md-6">
                <x-label for="name" text="{{ __('Tên') }}" icon="ti ti-calendar-plus" required="true" />
                <x-input name="name" :value="old('name')" :required="true" :placeholder="__('Tên của chương trình flash sale')" />
            </div>
            <div class="mb-3 col-md-3">
                <x-label for="start_time" text="{{ __('Thời gian bắt đầu') }}" icon="ti ti-clock" required="true" />
                <x-input class="flatpickr-dt" name="start_time" :required="true" />
            </div>
            <div class="mb-3 col-md-3">
                <x-label for="end_time" text="{{ __('Thời gian kết thúc') }}" icon="ti ti-clock" required="true" />
                <x-input class="flatpickr-dt" name="end_time" :required="true" />
            </div>
            <div class="col-12">
                @include('admin.flash_sales.partials.products')
            </div>
        </div>
    </div>
</div>
