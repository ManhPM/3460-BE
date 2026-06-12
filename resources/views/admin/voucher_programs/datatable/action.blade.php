<div class="d-flex">
    <a style="margin-left: 0.3rem" href="{{ route('admin.voucher_program.edit', $id) }}" class="ml-2">
        <i class="btn btn-info btn-icon ti ti-pencil"></i>
    </a>
    <x-button.modal-delete style="margin-left: 0.3rem" class="btn-icon"
        data-route="{{ route('admin.voucher_program.delete', $id) }}">
        <i class="ti ti-trash"></i>
    </x-button.modal-delete>
</div>
