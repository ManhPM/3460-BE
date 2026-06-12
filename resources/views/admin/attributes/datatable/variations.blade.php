@if (isset($variations) && count($variations) > 0)
    <p>
        @foreach ($variations as $item)
            @if ($loop->last)
                {{ $item['name'] }}
            @else
                {{ $item['name'] }},&nbsp;
            @endif
        @endforeach
    </p>
@endif
<x-link :href="route('admin.attribute.variation.index', $id)" :title="__('Cấu hình các biến thể')" />
