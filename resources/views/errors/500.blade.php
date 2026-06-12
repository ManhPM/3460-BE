<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi máy chủ nội bộ</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-500);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">500 - Lỗi máy chủ nội bộ</h2>
        <p class="message">Đã xảy ra lỗi trong quá trình xử lý. Vui lòng thử lại sau hoặc liên hệ với quản trị viên.
        </p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>

    @include('errors.scripts')
</body>

</html>
