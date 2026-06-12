<div class="toggle-columns-table">
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-success toggle-columns-btn" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasToggleColumns" aria-controls="offcanvasToggleColumns">
            <i class="ti ti-columns me-1"></i>
            <span class="toggle-columns-text">{{ __('Chọn cột') }}</span>
        </button>
    </div>
</div>

<!-- Offcanvas for Toggle Columns -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasToggleColumns"
    aria-labelledby="offcanvasToggleColumnsLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="offcanvasToggleColumnsLabel">
            <i class="ti ti-columns me-2"></i>{{ __('Chọn cột') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="drop-toggle-columns">
            <div class="text-muted text-center py-3">
                <i class="ti ti-loader-2 spinner-border spinner-border-sm me-2"></i>{{ __('loading') }}...
            </div>
        </div>
    </div>
</div>
