@extends('admin.layouts.guest.master')

@php
    $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
    $settings = $settingRepository->getAll();
@endphp

@section('content')
    <div class="page page-center background-container">
        <div class="container-tight py-4">
            <x-form :action="route('admin.login.post')" class="glass-card login-container" type="post" :validate="true">
                <div class="card-body p-3 shadow-sm">
                    <h2 class="card-title mb-3 text-center text-2xl font-bold text-white">
                        {{ __('Chào mừng tới trang Quản trị') }}
                    </h2>
                    <div class="logo-container mb-3 text-center">
                        <img style="max-height: 150px" class="img-fluid mx-auto"
                            src="{{ asset($settings->where('setting_key', 'site_logo')->first()->plain_value) }}"
                            alt="">
                    </div>
                    <p class="mb-3 text-center text-white font-bold" style="text-shadow: 2px 2px 3px rgba(0, 0, 0, 1);">
                        {{ __('Vui lòng nhập thông tin đăng nhập') }}
                    </p>
                    <div class="mb-3">
                        <label class="form-label mb-1 text-sm font-medium text-white">👤 {{ __('Tài khoản') }}</label>
                        <x-input name="email" :required="true"
                            class="form-input w-full rounded-lg px-3 py-2 focus:outline-none" :placeholder="__('Tài khoản')" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1 text-sm font-medium text-white">🔑 {{ __('Mật khẩu') }}</label>
                        <x-input-password name="password" :required="true"
                            class="form-input w-full rounded-lg px-3 py-2 focus:outline-none" :placeholder="__('Mật khẩu')" />
                    </div>
                    <div class="form-footer">
                        <button type="submit"
                            class="btn-gradient w-full rounded-lg py-2 font-semibold text-white focus:outline-none">
                            {{ __('Đăng nhập') }}
                        </button>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
@endsection

<style>
    .background-container {
        position: relative;
        width: 100%;
        height: 100vh;
        z-index: 1;
        background-image: url('{{ asset('assets/images/bg-admin.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(1px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        max-width: 380px;
        margin: 0 auto;
    }

    .btn-gradient {
        background: linear-gradient(135deg, #0056B3, #00A3FF) !important;
        transition: all 0.3s ease;
        border: none !important;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #00A3FF, #0056B3) !important;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 83, 179, 0.5);
    }

    .form-input,
    .form-control {
        background: rgba(255, 255, 255, 0.8) !important;
        border: 2px solid rgba(255, 255, 255, 0.5) !important;
        color: #333 !important;
        transition: all 0.3s ease;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        font-size: 0.9rem;
    }

    .form-input:focus,
    .form-control:focus {
        background: rgba(255, 255, 255, 0.95) !important;
        border-color: rgba(0, 123, 255, 0.7) !important;
        box-shadow: 0 0 15px rgba(0, 123, 255, 0.3) !important;
    }

    .form-input::placeholder,
    .form-control::placeholder {
        color: #666;
        opacity: 0.8;
    }

    .logo-container {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .login-container {
        animation: fadeIn 1s ease-out;
        z-index: 10;
        position: relative;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .page-center {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .form-label {
        color: #000 !important;
        font-weight: 500;
    }

    input.form-control,
    input.form-input {
        font-size: 0.9rem;
        font-weight: normal;
    }
</style>
