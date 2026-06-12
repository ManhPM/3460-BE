<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Truy cập bị từ chối</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-403);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">403 - Truy cập bị từ chối</h2>
        <p class="message">Bạn không có quyền truy cập trang này. Vui lòng kiểm tra lại quyền của bạn.</p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>

    @include('errors.scripts')
</body>

</html>
