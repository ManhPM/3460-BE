@extends('admin.layouts.master')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav class="fancy-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb-list">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">
                                    <span class="breadcrumb-icon">
                                        🏠
                                    </span>
                                    <span class="breadcrumb-text">{{ __('Dashboard') }}</span>
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <span class="breadcrumb-link">
                                    <span class="breadcrumb-icon">📍</span>
                                    <span class="breadcrumb-text">{{ __('profile') }}</span>
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <x-form :action="route('admin.profile.update')" type="put" enctype="multipart/form-data" :validate="true">
                        <div class="card">
                            <div class="card-header justify-content-center">
                                <h2 class="mb-0">{{ __('profile') }}</h2>
                            </div>
                            <div class="card-body">
                                <div class="mb-5 text-center">
                                    <label class="control-label d-block"><i class="ti ti-photo"></i>
                                        {{ __('avatar') }}:</label>
                                    <div class="d-inline-block" style="width: 200px; height: 200px; object-fit: cover;">
                                        <x-input-image-ckfinder name="avatar" showImage="avatar" :value="$auth->avatar ?? old('avatar')"
                                            style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>
                                </div>
                                <!-- firstname -->
                                <div class="mb-3">
                                    <label class="control-label"><i class="ti ti-user-edit"></i> {{ __('Tên') }}:
                                    </label>
                                    <x-input name="fullname" :value="$auth->fullname" :required="true"
                                        placeholder="{{ __('Tên') }}" />
                                </div>
                                <!-- phone -->
                                <div class="mb-3">
                                    <label class="control-label"><i class="ti ti-phone"></i> {{ __('phone') }}:
                                    </label>
                                    <x-input-phone name="phone" :value="$auth->phone" :required="true" :placeholder="__('phone')" />
                                </div>
                                @if ($auth->hasRole('branch'))
                                    <!-- email -->
                                    <div class="mb-3">
                                        <label class="control-label"><i class="ti ti-mail"></i> {{ __('Email') }}:
                                        </label>
                                        <x-input-email name="email" :value="$auth->email" :required="true"
                                            placeholder="{{ __('Email') }}" />
                                    </div>
                                    <!-- branch_name -->
                                    <div class="mb-3">
                                        <label class="control-label"><i class="ti ti-building-store"></i>
                                            {{ __('Tên thương hiệu') }}:
                                        </label>
                                        <x-input name="branch_name" :value="$auth->branch_name"
                                            placeholder="{{ __('Tên thương hiệu') }}" />
                                    </div>
                                    <!-- branch_phone -->
                                    <div class="mb-3">
                                        <label class="control-label"><i class="ti ti-phone"></i>
                                            {{ __('Số điện thoại chi nhánh') }}:
                                        </label>
                                        <x-input name="branch_phone" :value="$auth->branch_phone"
                                            placeholder="{{ __('Số điện thoại chi nhánh') }}" />
                                    </div>
                                    <!-- branch_address -->
                                    <div class="mb-3">
                                        <label class="control-label"><i class="ti ti-map-pin"></i>
                                            {{ __('Địa chỉ chi nhánh') }}:
                                        </label>
                                        <x-input name="branch_address" :value="$auth->branch_address"
                                            placeholder="{{ __('Địa chỉ chi nhánh') }}" />
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer mt-auto bg-transparent">
                                <div class="btn-list justify-content-center">
                                    <x-button.submit :title="__('update')" />
                                </div>
                            </div>
                        </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-js')
    @include('ckfinder::setup')
@endpush
