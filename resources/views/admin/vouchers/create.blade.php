@extends('admin.layouts.master')
@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form id="formVoucher" :action="route('admin.voucher.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.vouchers.forms.create-left')
                    @include('admin.vouchers.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.vouchers.scripts.scripts')
@endpush
