@push('custom-css')
    <style>
        .pac-container {
            z-index: 99999999 !important;
        }
    </style>
@endpush
<div class="modal modal-blur fade" id="modalPickEndAddress" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Chọn địa chỉ nhận hàng') }}</h5>
                <button type="button" class="btn-close cancel-pick-address" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="" class="form-label">{{ __('Chọn địa chỉ nhận hàng') }}</label>
                    <x-input name="pickEndPlace" id="pickEndPlace" />

                </div>
                <div id="pickedEndAddress" class="mb-3">
                    <span><strong>{{ __('Địa chỉ nhận hàng đã chọn') }}</strong></span>:
                    <span class="show-text"></span>
                </div>
                <div id="showEndMap" class="w-100" style="height: 400px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto cancel-pick-address"
                    data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <button type="button" id="confirmPickEndAddress" class="btn btn-danger"
                    data-bs-dismiss="modal">{{ __('Xác nhận') }}</button>
            </div>
        </div>
    </div>
</div>
