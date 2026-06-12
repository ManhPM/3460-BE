<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin flash sale #:id', ['id' => $instance->id]) }}</h2>
            <x-input class="hidden-flashsale-id" type="hidden" :value="$instance->id" />
        </div>
        <div class="row card-body">
            <h3>{{ __('Thông tin chung') }}</h3>
            <div class="mb-3 col-md-6">
                <x-label for="name" text="{{ __('Tên') }}" icon="ti ti-calendar-plus" required="true" />
                <x-input name="name" :value="$instance->name" :required="true" />
            </div>
            <div class="mb-3 col-md-3">
                <x-label for="start_time" text="{{ __('Thời gian bắt đầu') }}" icon="ti ti-clock" required="true" />
                <x-input class="flatpickr-dt" :value="format_datetime($instance->start_time)" name="start_time" :required="true" />
            </div>
            <div class="mb-3 col-md-3">
                <x-label for="end_time" text="{{ __('Thời gian kết thúc') }}" icon="ti ti-clock" required="true" />
                <x-input class="flatpickr-dt" :value="format_datetime($instance->end_time)" name="end_time" :required="true" />
            </div>
            <div class="col-12">
                @include('admin.flash_sales.partials.products')
            </div>
        </div>
    </div>
</div>
