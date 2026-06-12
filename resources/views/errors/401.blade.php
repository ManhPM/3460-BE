<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Chưa xác thực</title>
    @include('errors.style')
</head>

<body style="--error-color: var(--error-401);">
    <div class="stars"></div>
    <div class="container">
        <h1 class="title">Ôi không!</h1>
        <h2 class="subtitle">401 - Chưa xác thực</h2>
        <p class="message">Bạn cần đăng nhập để truy cập trang này. Vui lòng đăng nhập và thử lại.</p>
        <a href="{{ Route::has('user.index') ? route('user.index') : route('admin.dashboard') }}" class="button">VỀ
            TRANG CHỦ</a>
    </div>

    @include('errors.scripts')
</body>

</html>
