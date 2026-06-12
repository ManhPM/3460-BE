<div class="col-12 col-md-9">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h2 class="mb-0 fs-2">
                <i class="ti ti-file-invoice me-2"></i>
                {{ __('Thông tin giao dịch') . ' #' . $commissionWithdrawal->id }}
            </h2>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-12">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-primary-subtle rounded-circle">
                                        <i class="ti ti-user"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Tên khách hàng') }}</div>
                                    <x-link :href="route('admin.user.edit', $commissionWithdrawal->user->id)" :title="$commissionWithdrawal->user->fullname" class="fw-medium" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-primary-subtle rounded-circle">
                                        <i class="ti ti-building-bank"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Tên ngân hàng') }}</div>
                                    <div class="fw-medium">{{ $commissionWithdrawal->user->bank_name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-primary-subtle rounded-circle">
                                        <i class="ti ti-user-bolt"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Tên chủ tài khoản') }}</div>
                                    <div class="fw-medium">{{ $commissionWithdrawal->user->bank_account }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-primary-subtle rounded-circle">
                                        <i class="ti ti-cash"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Số tài khoản') }}</div>
                                    <div class="fw-medium">{{ $commissionWithdrawal->user->bank_account_number }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-success-subtle rounded-circle">
                                        <i class="ti ti-currency-dollar"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Số tiền rút') }}</div>
                                    <div class="fs-4 fw-semibold text-success">
                                        {{ format_price($commissionWithdrawal->amount) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggleShippingInfoOther').addEventListener('change', function() {
        var shippingInfoDiv = document.getElementById('infoShippingOther');
        shippingInfoDiv.classList.toggle('d-none')
    });
</script>
