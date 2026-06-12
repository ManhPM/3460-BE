<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<meta name="X-TOKEN" content="{{ csrf_token() }}">
<meta name="url-home" content="{{ url('/') }}">
<meta name="currency" content="{{ config('custom.currency') }}">
<meta name="position_currency" content="{{ config('custom.format.position_currency') }}">
<title>@yield('title', 'Admin')</title>
@php
    $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
    $icon = $settingRepository->findByField('setting_key', 'site_icon')->plain_value;
@endphp
<link rel="shortcut icon" type="image/x-icon" href="{{ asset($icon) }}" />
<!-- CSS files -->
<script src="https://cdn.jsdelivr.net/npm/imask"></script>
<link href="{{ asset('/public/libs/tabler/dist/css/tabler.min.css') }}" rel="stylesheet" />
<link href="{{ asset('/public/libs/tabler/dist/css/tabler-vendors.min.css') }}" rel="stylesheet" />
<link href="{{ asset('public/libs/tabler/plugins/tabler-icon/webfont/tabler-icons.min.css') }}"
    rel="stylesheet"type="text/css">
<link href="{{ asset('public/libs/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet"type="text/css">
<link href="{{ asset('public/libs/Parsley.js-2.9.2/style.css') }}" rel="stylesheet">
<!-- datatable -->
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('/public/libs/datatables/plugins/bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('/public/libs/datatables/plugins/buttons/css/buttons.bootstrap5.min.css') }}">
<link rel="stylesheet"
    href="{{ asset('/public/libs/datatables/plugins/responsive/css/responsive.bootstrap5.min.css') }}">
<style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
        --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
        font-feature-settings: "cv03", "cv04", "cv11";
    }
</style>
<link href="{{ asset('public/admin/assets/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('public/admin/assets/css/datatable.css') }}" rel="stylesheet">

@stack('libs-css')
@stack('custom-css')

@include('admin.layouts.sb-admin-pro.style-script')
