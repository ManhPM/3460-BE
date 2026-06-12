@foreach ($parents_name as $parent)
    <x-link :href="route('admin.post_category.edit', $parent->id)" :title="$parent->name" />
@endforeach
