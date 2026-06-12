@extends('admin.layouts.master')
@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form id="formVoucherProgram" :action="route('admin.voucher_program.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$instance->id" />
                <div class="row justify-content-center">
                    @include('admin.voucher_programs.forms.edit-left')
                    @include('admin.voucher_programs.forms.edit-right')
                </div>
            </x-form>
            <x-form :action="route('admin.voucher_program.giveVoucher')" type="post" :validate="true">
                <x-input type="hidden" name="id" :value="$instance->id" />
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-1 mt-3">
                                    <div class="card-header justify-content-between">
                                        <h2 class="mb-0">{{ __('Phát voucher cho khách hàng') }}</h2>
                                    </div>
                                    <div class="card-body row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <x-label for="" text="{{ __('Đối tượng được nhận voucher') }}" icon="ti ti-tag" required="true" />
                                                <x-select class="option" name="option" :required="true">
                                                    @foreach ($options as $key => $value)
                                                        <x-select-option :value="$key" :title="$value" />
                                                    @endforeach
                                                </x-select>
                                            </div>
                                        </div>
                                        <div style="display: none" id="notification-customer-select" class="col-12">
                                            <div class="mb-3">
                                                <x-label for="" text="{{ __('Khách hàng') }}" icon="ti ti-user" required="true" />
                                                <x-select name="user_id[]" class="select2-bs5-ajax" :data-url="route('admin.search.select.user')"
                                                    id="user_id" multiple>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center h-100 gap-2">
                                            <button id="submitGiveVoucher" class="btn btn-default-cms" type="submit"><i
                                                    class="ti ti-gift me-2"></i>Phát
                                                voucher</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-form>
            <x-form :action="route('admin.voucher_program.reset')" type="post" :validate="true">
                <x-input type="hidden" name="id" :value="$instance->id" />
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-5 mt-3">
                                    <div class="card-header justify-content-between">
                                        <h2 class="mb-0">{{ __('Reset các lượt thu thập voucher của khách hàng') }}</h2>
                                    </div>
                                    <div class="card-body row">
                                        <div class="d-flex align-items-center h-100 gap-2">
                                            <button class="btn btn-default-cms" type="submit"><i
                                                    class="ti ti-gift me-2"></i>Reset thu thập
                                                voucher</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.voucher_programs.scripts.scripts')
@endpush
