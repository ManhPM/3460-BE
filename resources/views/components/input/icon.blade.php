<x-select name="{{ $name }}" id="icon-select-{{ $name }}" class="select2-bs5-ajax"
    data-url="{{ route('admin.search.select.icon') }}" :required="true">
    <x-select-option :option="$value" :value="$value" :title="$value" />
</x-select>
