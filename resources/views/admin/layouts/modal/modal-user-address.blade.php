<div class="modal modal-blur fade" id="modalPickAddressUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Chọn địa chỉ') }}</h5>
                <button type="button" class="btn-close cancel-pick-address" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="" class="form-label">{{ __('Chọn địa chỉ') }}</label>
                    <x-input name="pickPlace" id="pickPlaceUser" />
                </div>
                <div id="pickedAddressUser" class="mb-3">
                    <span><strong>{{ __('Địa chỉ đã chọn') }}</strong>:</span>
                    <span class="show-text"></span>
                </div>
                <div id="showMapUser" class="w-100" style="height: 400px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto cancel-pick-address"
                    data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <button type="button" id="confirmPickAddressUser" class="btn btn-danger"
                    data-bs-dismiss="modal">{{ __('Xác nhận') }}</button>
            </div>
        </div>
    </div>
</div>
