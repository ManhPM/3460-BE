<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-404);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">404 - Không tìm thấy trang</h2>
        <p class="message">Trang bạn đang tìm có thể đã bị xóa, đổi tên hoặc tạm thời không khả dụng.</p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>

    @include('errors.scripts')
</body>

</html>
