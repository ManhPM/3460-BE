<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 - Yêu cầu không hợp lệ</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-400);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">400 - Yêu cầu không hợp lệ</h2>
        <p class="message">Yêu cầu của bạn không hợp lệ hoặc bị lỗi. Vui lòng kiểm tra lại và thử lại.</p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>

    @include('errors.scripts')
</body>

</html>
