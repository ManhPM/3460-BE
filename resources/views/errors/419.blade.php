<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Phiên hết hạn</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-419);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">419 - Trang đã hết hạn</h2>
        <p class="message">Trang đã hết hạn do không hoạt động. Vui lòng làm mới trang và thử lại.</p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>

    @include('errors.scripts')
</body>

</html>
