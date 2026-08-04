@if (!empty($is_phone_verified) || !empty($phone_verified))
    <span class="badge bg-green">Đã xác thực</span>
@else
    <span class="badge bg-red">Chưa xác thực</span>
@endif
