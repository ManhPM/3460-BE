@extends('admin.layouts.master')

@push('libs-css')
@endpush
@push('custom-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Danh sách đơn hàng') }}</h2>
                    <x-link :href="route('admin.order.create')" class="btn btn-default-cms"><i
                            class="ti ti-plus me-1"></i>{{ __('Thêm') }}</x-link>
                </div>
                <div class="card-body">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{ $dataTable->table(['class' => 'table table-bordered', 'style' => 'min-width: 900px;'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])

    <script>
        $(document).on('click', '#confirm-order', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            Swal.fire({
                title: "Bạn có chắc chắn muốn duyệt đơn này?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#1c5639",
                cancelButtonColor: "#d33",
                confirmButtonText: '<i class="ti ti-check"></i> Xác nhận',
                cancelButtonText: '<i class="ti ti-x"></i> Quay lại',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        $(document).on('click', '#cancel-order', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            Swal.fire({
                title: "Bạn có chắc chắn muốn từ chối đơn này?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#1c5639",
                cancelButtonColor: "#d33",
                confirmButtonText: '<i class="ti ti-check"></i> Xác nhận',
                cancelButtonText: '<i class="ti ti-x"></i> Quay lại',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endpush
