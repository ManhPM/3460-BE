<div class="d-flex">
    <a href="{{ route('admin.shipping_rate.edit', $id) }}" class="ml-2">
        <i class="btn btn-info btn-icon ti ti-pencil"></i>
    </a>
    <x-button.modal-delete style="margin-left: 0.3rem" class="btn-icon"
        data-route="{{ route('admin.shipping_rate.delete', $id) }}">
        <i class="ti ti-trash"></i>
    </x-button.modal-delete>
</div>
