@include('admin.dashboard.script.choose-date-filter')
<div class="row mb-4">
    <div class="col-12">
        <div class="card mt-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.dashboard') }}" id="dateFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="datePreset" class="form-label"><i
                                    class="ti ti-calendar me-1"></i>{{ __('Chọn khoảng thời gian') }}</label>
                            <select class="form-select" id="datePreset" name="date_preset">
                                <option value="">{{ __('Tùy chọn') }}</option>
                                <option value="today">{{ __('Hôm nay') }}</option>
                                <option value="yesterday">{{ __('Hôm qua') }}</option>
                                <option value="this_week">{{ __('Tuần này') }}</option>
                                <option value="last_week">{{ __('Tuần trước') }}</option>
                                <option value="this_month">{{ __('Tháng này') }}</option>
                                <option value="month_1">{{ __('Tháng 1') }}</option>
                                <option value="month_2">{{ __('Tháng 2') }}</option>
                                <option value="month_3">{{ __('Tháng 3') }}</option>
                                <option value="month_4">{{ __('Tháng 4') }}</option>
                                <option value="month_5">{{ __('Tháng 5') }}</option>
                                <option value="month_6">{{ __('Tháng 6') }}</option>
                                <option value="month_7">{{ __('Tháng 7') }}</option>
                                <option value="month_8">{{ __('Tháng 8') }}</option>
                                <option value="month_9">{{ __('Tháng 9') }}</option>
                                <option value="month_10">{{ __('Tháng 10') }}</option>
                                <option value="month_11">{{ __('Tháng 11') }}</option>
                                <option value="month_12">{{ __('Tháng 12') }}</option>
                                <option value="quarter_1">{{ __('Quý 1') }}</option>
                                <option value="quarter_2">{{ __('Quý 2') }}</option>
                                <option value="quarter_3">{{ __('Quý 3') }}</option>
                                <option value="quarter_4">{{ __('Quý 4') }}</option>
                                <option value="this_year">{{ __('Năm nay') }}</option>
                                <option value="last_year">{{ __('Năm trước') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="from_date" class="form-label"><i
                                    class="ti ti-calendar me-1"></i>{{ __('Từ ngày') }}</label>
                            <input class="form-control flatpickr" id="from_date" name="from_date"
                                value="{{ $fromDate ? format_date($fromDate) : '' }}"
                                placeholder="{{ __('Ví dụ: 01-01-2024') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label"><i
                                    class="ti ti-calendar me-1"></i>{{ __('Đến ngày') }}</label>
                            <input class="form-control flatpickr" id="to_date" name="to_date"
                                value="{{ $toDate ? format_date($toDate) : '' }}"
                                placeholder="{{ __('Ví dụ: 31-12-2024') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter me-1"></i> {{ __('Lọc') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="resetFilter">
                                <i class="ti ti-refresh me-1"></i> {{ __('Đặt lại') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
