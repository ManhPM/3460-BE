@if (!empty($is_email_verified) || !empty($email_verified_at))
    <span class="badge bg-green">Đã xác thực</span>
@else
    <span class="badge bg-red">Chưa xác thực</span>
@endif
