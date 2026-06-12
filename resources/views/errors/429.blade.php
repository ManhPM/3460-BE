<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Quá nhiều yêu cầu</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-429);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">429 - Quá nhiều yêu cầu</h2>
        <p class="message">Bạn đã gửi quá nhiều yêu cầu trong một khoảng thời gian ngắn. Vui lòng thử lại sau.</p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>

    @include('errors.scripts')
</body>

</html>
