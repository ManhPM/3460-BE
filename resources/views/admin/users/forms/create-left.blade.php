<div class="col-12 col-md-9">
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black active" id="tab-login" data-bs-toggle="tab" data-bs-target="#pane-login"
                type="button" role="tab" aria-controls="pane-login"
                aria-selected="true">{{ __('Thông tin đăng nhập') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black" id="tab-basic" data-bs-toggle="tab" data-bs-target="#pane-basic"
                type="button" role="tab" aria-controls="pane-basic"
                aria-selected="false">{{ __('Thông tin cơ bản') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-black" id="tab-affiliate" data-bs-toggle="tab" data-bs-target="#pane-affiliate"
                type="button" role="tab" aria-controls="pane-affiliate"
                aria-selected="false">{{ __('Thông tin Affiliate') }}</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pane-login" role="tabpanel" aria-labelledby="tab-login">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Thông tin đăng nhập') }}</h2>
                </div>
                <div class="row card-body">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="phone" text="{{ __('Số điện thoại') }}" icon="ti ti-phone" />
                            <x-input-phone name="phone" :value="old('phone')" placeholder="{{ __('Số điện thoại') }}" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="email" text="{{ __('Email đăng nhập') }}" icon="ti ti-mail" />
                            <x-input-email id="emailInput" name="email" :value="old('email')" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="password" text="{{ __('Mật khẩu') }}" icon="ti ti-key" />
                            <x-input-password name="password" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="password_confirmation" text="{{ __('Xác nhận mật khẩu') }}"
                                icon="ti ti-key" />
                            <x-input-password name="password_confirmation" data-parsley-equalto="input[name='password']"
                                data-parsley-equalto-message="{{ __('Mật khẩu không khớp.') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-basic" role="tabpanel" aria-labelledby="tab-basic">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Thông tin cơ bản') }}</h2>
                </div>
                <div class="row card-body">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="fullname" text="{{ __('Họ và tên') }}" icon="ti ti-user-edit" />
                            <x-input name="fullname" :value="old('fullname')" placeholder="{{ __('Họ và tên') }}" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="birthday" text="{{ __('Ngày sinh') }}" icon="ti ti-calendar" />
                            <x-input class="flatpickr" name="birthday" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <x-label for="gender" text="{{ __('Giới tính') }}" icon="ti ti-gender-female" />
                            <x-select name="gender">
                                @foreach ($gender as $key => $value)
                                    <x-select-option :value="$key" :title="__($value)" />
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pane-affiliate" role="tabpanel" aria-labelledby="tab-affiliate">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="mb-0">{{ __('Thông tin Affiliate') }}</h2>
                </div>
                <div class="row card-body">
                    <div class="mb-3">
                        <x-label for="bank_name" text="{{ __('Tên ngân hàng nhận hoa hồng') }}"
                            icon="ti ti-building-bank" required="true" />
                        <x-input name="bank_name" placeholder="{{ __('Tên ngân hàng nhận hoa hồng') }}" />
                    </div>
                    <div class="mb-3">
                        <x-label for="bank_account" text="{{ __('Tên chủ tài khoản nhận hoa hồng') }}"
                            icon="ti ti-user-dollar" required="true" />
                        <x-input name="bank_account" placeholder="{{ __('Tên chủ tài khoản nhận hoa hồng') }}" />
                    </div>
                    <div class="mb-3">
                        <x-label for="bank_account_number" text="{{ __('Số tài khoản nhận hoa hồng') }}"
                            icon="ti ti-credit-card-refund" required="true" />
                        <x-input name="bank_account_number" placeholder="{{ __('Số tài khoản nhận hoa hồng') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
