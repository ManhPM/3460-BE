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
            @if (!empty($instance->user_id))
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="ti ti-user me-2"></i>{{ __('Thông tin người dùng') }}
                                </h5>
                                <a href="{{ route('admin.user.edit', ['id' => $instance->user_id]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti ti-external-link me-1"></i>{{ __('Xem chi tiết người dùng') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
