@props(['name', 'value' => '', 'showImage', 'small' => false])

<div class="image-ckfinder-wrapper {{ $small ? 'is-small' : '' }}">
    <input type="text" {{ $attributes->class(['d-none']) }} name="{{ $name }}" value="{{ $value }}">
    <div class="image-preview-container {{ $small ? 'is-small' : '' }}">
        <img id="{{ $showImage }}" class="add-image-ckfinder pointer image-preview {{ $small ? 'is-small' : '' }}"
            data-preview="#{{ $showImage }}" data-input="input[name='{{ $name }}']" data-type=""
            src="{{ asset($value ? $value : 'assets/images/default.png') }}" alt="Preview Image">
    </div>
</div>

<style>
    .image-ckfinder-wrapper {
        position: relative;
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }

    .image-ckfinder-wrapper.is-small {
        width: auto;
        display: inline-block;
        max-width: 180px;
    }

    .image-ckfinder-wrapper:hover {
        border-color: #007bff;
    }

    .image-preview-container {
        position: relative;
        width: 100%;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-preview-container.is-small {
        min-height: 100px;
        padding: 8px;
    }

    .image-preview {
        width: 100%;
        height: auto;
        object-fit: contain;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .image-preview.is-small {
        width: auto;
        max-width: 160px;
        max-height: 120px;
    }

    .image-preview:hover {
        transform: scale(1.02);
    }

    .image-preview-container:empty::before {
        content: "Click để thêm ảnh";
        color: #6c757d;
        font-style: italic;
    }
</style>
