<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Thông tin đơn liên hệ') }}</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <x-label for="content" text="{{ __('Nội dung') }}" icon="ti ti-pencil" required="true" />
                        <textarea name="content" class="form-control" rows="5">{{ $instance->content }}</textarea>
                    </div>
                </div>
            </div>
            @if ($instance->type == App\Enums\Contact\ContactType::AffiliateRegister->value)
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="ti ti-info-circle me-2"></i>{{ __('Thông tin đăng ký Affiliate') }}
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <p class="mb-2"><strong>{{ __('Tên ngân hàng') }}:</strong>
                                            {{ $instance->bank_name }}</p>
                                        <p class="mb-2"><strong>{{ __('Tên chủ tài khoản') }}:</strong>
                                            {{ $instance->bank_account }}</p>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p class="mb-2"><strong>{{ __('Số tài khoản') }}:</strong>
                                            {{ $instance->bank_account_number }}</p>
                                    </div>
                                </div>
                                <hr>
                                <a href="{{ route('admin.user.edit', ['id' => $instance->user_id]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-user me-1"></i>{{ __('Xem thông tin người dùng') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="ti ti-info-circle me-2"></i>{{ __('Thông tin Affiliate cũ') }}
                                </h5>
                                <p class="mb-2"><strong>{{ __('Tên ngân hàng') }}:</strong>
                                    {{ $instance->user->bank_name }}</p>
                                <p class="mb-2"><strong>{{ __('Tên chủ tài khoản') }}:</strong>
                                    {{ $instance->user->bank_account }}</p>
                                <p class="mb-0"><strong>{{ __('Số tài khoản') }}:</strong>
                                    {{ $instance->user->bank_account_number }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10 border-success mb-3 text-white">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="ti ti-info-circle me-2"></i>{{ __('Thông tin Affiliate mới') }}
                                </h5>
                                <p class="mb-2"><strong>{{ __('Tên ngân hàng') }}:</strong>
                                    {{ $instance->bank_name }}</p>
                                <p class="mb-2"><strong>{{ __('Tên chủ tài khoản') }}:</strong>
                                    {{ $instance->bank_account }}</p>
                                <p class="mb-0"><strong>{{ __('Số tài khoản') }}:</strong>
                                    {{ $instance->bank_account_number }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="ti ti-user me-2"></i>{{ __('Thông tin người dùng') }}
                                </h5>
                                <a href="{{ route('admin.user.edit', ['id' => $instance->user_id]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-external-link me-1"></i>{{ __('Xem chi tiết') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
