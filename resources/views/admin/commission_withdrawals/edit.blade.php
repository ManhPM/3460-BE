@extends('admin.layouts.master')
@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@push('custom-css')
    <style>
        .product-variations {
            list-style: none;
        }

        .product-variations li {
            padding: 5px;
            cursor: default;
        }

        .product-variations li:hover {
            background-color: gainsboro;
        }

        .remove-item-product:hover {
            background-color: #f3dbdb;
        }
    </style>
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.commission_withdrawal.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$commissionWithdrawal->id" />
                <div class="row justify-content-center">
                    @include('admin.commission_withdrawals.forms.edit-left')
                    @include('admin.commission_withdrawals.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('/public/libs/jquery-throttle-debounce/jquery.ba-throttle-debounce.min.js') }}"></script>
@endpush
