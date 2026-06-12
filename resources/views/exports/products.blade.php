<table>
    <thead>
        <tr>
            <th>id</th>
            <th>_lft</th>
            <th>_rgt</th>
            <th>parent_id</th>
            <th>name</th>
            <th>slug</th>
            <th>avatar</th>
            <th>icon</th>
            <th>position</th>
            <th>is_active</th>
            <th>is_home</th>
            <th>created_at</th>
            <th>updated_at</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->_lft }}</td>
                <td>{{ $item->_rgt }}</td>
                <td>{{ $item->parent_id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->slug }}</td>
                <td>{{ $item->avatar }}</td>
                <td>{{ $item->icon }}</td>
                <td>{{ $item->position }}</td>
                <td>{{ $item->is_active }}</td>
                <td>{{ $item->is_home }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
