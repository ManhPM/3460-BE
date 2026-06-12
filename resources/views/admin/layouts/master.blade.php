<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('admin.layouts.head')
</head>

<body>
    <div class="page layout overflow-x-hidden">
        <x-admin-sidebar-left />
        @include('admin.layouts.sidebar-top')
        <div class="page-wrapper">
            <div class="main-content" id="mainContent">
                @section('breadcrumbs')
                    @include('admin.layouts.partials.breadcrumbs')
                @show
                @yield('content')
            </div>
            @include('admin.layouts.modal.modal-logout')
            @include('admin.layouts.modal.modal-delete')
        </div>
    </div>
    @include('admin.layouts.scripts')
    @if (env('IS_PRO'))
        @include('admin.scripts.firebase-script')
    @endif
    @include('libs.all-in-one-flatpickr')
    <x-alert />
</body>

</html>
