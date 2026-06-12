<button type="button"
    {{ $attributes->class(['btn', 'btn-danger', 'open-modal-delete'])->merge([
        'data-bs-toggle' => 'modal',
        'data-bs-target' => '#modalDelete',
    ]) }}>
    @if ($slot->isEmpty())
        <i class="ti ti-trash"></i>
        <span class="ms-2">{{ $title ?? '' }}</span>
    @else
        <span>{{ $slot }}</span>
    @endif
</button>
