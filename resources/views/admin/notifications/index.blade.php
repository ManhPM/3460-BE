@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Danh sách thông báo') }}</h2>
                    <x-link :href="route('admin.notification.create')" class="btn btn-default-cms"><i
                            class="ti ti-plus me-1"></i>{{ __('Thêm thông báo') }}</x-link>
                </div>

                <div class="card-body">
                    <x-form id="formMultiple" :action="route('admin.notification.multiple')" type="post" :validate="true">
                        <div class="table-responsive position-relative">
                            <x-admin.partials.toggle-column-datatable />
                            @isset($actionMultiple)
                                <x-admin.partials.select-action-multiple :actionMultiple="$actionMultiple" />
                            @endisset
                            {{ $dataTable->table(['class' => 'table table-bordered'], true) }}
                        </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])
@endpush
