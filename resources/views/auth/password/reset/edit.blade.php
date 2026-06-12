@extends('admin.layouts.guest.master')

@section('content')
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="mb-4 text-center">
                <img src="{{ asset(config('custom.images.logo')) }}" width="200" alt="">
            </div>
            <x-form :action="route('user.password.reset.api.update')" class="card card-md" type="put" :validate="true">
                <x-input type="hidden" name="token" :value="$token" />
                <x-input type="hidden" name="code" :value="$code" />
                <div class="card-body">
                    <h2 class="card-title mb-4 text-center">{{ __('Nhập mật khẩu mới') }}</h2>
                    <div class="mb-3">
                        <x-label text="{{ __('Mật khẩu mới') }}" />
                        <x-input-password name="password" :required="true" :placeholder="__('Mật khẩu mới')" />
                    </div>
                    <div class="mb-3">
                        <x-label text="{{ __('Nhập lại mật khẩu') }}" />
                        <x-input-password name="password_confirmation" :required="true" :placeholder="__('Nhập lại mật khẩu')"
                            data-parsley-equalto="input[name='password']"
                            data-parsley-equalto-message="{{ __('Mật khẩu không khớp.') }}" />
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Lấy lại mật khẩu') }}</button>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
@endsection
