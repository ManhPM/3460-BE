<script>
    $(document).ready(function() {
        @foreach ($type as $value)
            @if ($message = Session::get($value))
                $.toast({
                    heading: '{{ $title }}',
                    text: '{{ $message }}',
                    position: '{{ $position }}',
                    icon: '{{ $value }}',
                    hideAfter: 10000
                });
            @endif
        @endforeach

        @if (isset($errors) && $errors->any())
            $.toast({
                heading: '{{ $title }}',
                text: '{{ $errors->first() }}', // Lấy lỗi đầu tiên
                position: '{{ $position }}',
                icon: 'warning',
                hideAfter: 10000
            });
        @endif
    });
</script>
