<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Dịch vụ tạm thời không khả dụng</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-503);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">503 - Dịch vụ tạm thời không khả dụng</h2>
        <p class="message">Dịch vụ đang được bảo trì hoặc quá tải. Vui lòng thử lại sau.</p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>
    @include('errors.scripts')
</body>

</html>
