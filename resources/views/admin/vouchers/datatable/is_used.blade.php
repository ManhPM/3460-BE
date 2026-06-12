@if ($is_used)
				<span @class(['badge', 'bg-success'])>Đã sử dụng</span>
@else
				<span @class(['badge', 'bg-warning'])>Chưa sử dụng</span>
@endif
