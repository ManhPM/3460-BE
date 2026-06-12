<div class="btn-list">
    <a href="{{ route('admin.wallet_transaction.show', $id) }}" class="btn btn-info btn-sm" title="{{ __('Xem chi tiết') }}">
        <i class="ti ti-eye"></i>
    </a>
    <form action="{{ route('admin.wallet_transaction.approve', $id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success btn-sm" {{ $status !== 'pending' ? 'disabled' : '' }} title="{{ __('Duyệt') }}">
            <i class="ti ti-check"></i>
        </button>
    </form>
    <form action="{{ route('admin.wallet_transaction.reject', $id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm" {{ $status !== 'pending' ? 'disabled' : '' }} title="{{ __('Hủy') }}">
            <i class="ti ti-x"></i>
        </button>
    </form>
</div>


