@php
    $previewId = 'video-preview-' . $name;
    $modalId = 'video-modal-' . $name;
    $containerId = 'video-container-' . $name;
    $inputId = 'video-input-' . $name;
    $fileInputId = 'video-file-input-' . $name;
    $varSuffix = str_replace(['[', ']'], '_', $name);

    $componentId = 'video-selector-' . $varSuffix;
    $previewSectionId = 'video-preview-section-' . $varSuffix;

    // Decode JSON string if needed and handle escaping
    if (isset($value) && is_string($value)) {
        $selectedVideos = json_decode(str_replace('\/', '/', $value), true) ?? [];
    } else {
        $selectedVideos = isset($value) && is_array($value) ? $value : [];
    }
@endphp

<div class="video-selector" id="{{ $componentId }}">
    <!-- Preview Area -->
    <div class="video-preview-section" id="{{ $previewSectionId }}">
        @if (count($selectedVideos) > 0)
            <div class="video-preview-grid">
                @foreach ($selectedVideos as $index => $videoUrl)
                    <div class="video-preview-item" data-url="{{ $videoUrl }}">
                        <video class="video-preview-video" controls>
                            <source src="{{ asset($videoUrl) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="video-preview-actions">
                            <button type="button" class="btn-remove"
                                onclick="removeSelectedVideo_{{ $varSuffix }}('{{ $videoUrl }}')"
                                title="Remove video">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <div class="video-preview-order">{{ $index + 1 }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-video">
                <i class="ti ti-video"></i>
                <span>No videos selected</span>
            </div>
        @endif
    </div>

    <!-- Select Button -->
    <button type="button" class="btn btn-outline-primary w-100"
        onclick="document.getElementById('{{ $fileInputId }}').click()">
        <i class="ti ti-video me-1"></i>{{ __('select_video') }}
        <span class="badge bg-secondary ms-1"
            id="selected-count-{{ $varSuffix }}">{{ count($selectedVideos) }}</span>
    </button>
    <div id="video-hidden-files-{{ $varSuffix }}"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="ti ti-video me-2"></i>
                    {{ __('select_video') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="video-search-{{ $varSuffix }}"
                        placeholder="{{ __('search_by_filename') }}">
                </div>

                <!-- Selected Videos Actions -->
                <div class="mb-3" id="video-actions-{{ $varSuffix }}" style="display: none;">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="clearAllSelected_{{ $varSuffix }}()">
                            <i class="ti ti-trash me-1"></i>Clear All
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="sortSelected_{{ $varSuffix }}()">
                            <i class="ti ti-arrows-sort me-1"></i>Sort
                        </button>
                    </div>
                </div>

                <!-- Videos Container -->
                <div id="{{ $containerId }}" class="video-images-grid">
                </div>

                <div class="text-center mt-3">
                    <button type="button" id="video-load-more-{{ $varSuffix }}" class="btn btn-outline-primary"
                        onclick="loadVideoFiles_{{ $varSuffix }}(true)" style="display:none">
                        {{ __('load_more') }}
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <input type="file" id="{{ $fileInputId }}" accept="video/*" multiple style="display: none;"
                    onchange="handleVideoFileSelect_{{ $varSuffix }}(this.files)">
                <button type="button" class="btn btn-success"
                    onclick="document.getElementById('{{ $fileInputId }}').click()">
                    <i class="ti ti-upload me-1"></i>{{ __('upload') }}
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="confirmVideoSelection_{{ $varSuffix }}()">
                    <i class="ti ti-check me-1"></i>{{ __('select_video') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let videoPage_{{ $varSuffix }} = 1;
    let videoLimit_{{ $varSuffix }} = 32;
    let videoHasMore_{{ $varSuffix }} = true;
    let videoIsLoading_{{ $varSuffix }} = false;

    let videoAllFiles_{{ $varSuffix }} = [];
    let videoFilteredFiles_{{ $varSuffix }} = [];
    let videoSelectedVideos_{{ $varSuffix }} = @json($selectedVideos);
    let videoSelectedFiles_{{ $varSuffix }} = [];

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Modal event - load videos when opened
        document.getElementById('{{ $modalId }}').addEventListener('shown.bs.modal', function() {
            // Reset và load lại videos mỗi khi modal mở
            videoPage_{{ $varSuffix }} = 1;
            videoHasMore_{{ $varSuffix }} = true;
            videoAllFiles_{{ $varSuffix }} = [];
            videoFilteredFiles_{{ $varSuffix }} = [];
            loadVideoFiles_{{ $varSuffix }}();

            updateVideoModalCount_{{ $varSuffix }}();
            updateVideoActions_{{ $varSuffix }}();
        });

        // Search functionality
        document.getElementById('video-search-{{ $varSuffix }}').addEventListener('input', function(e) {
            filterVideoFiles_{{ $varSuffix }}(e.target.value);
        });

        // Initialize input value on load
        updateVideoInput_{{ $varSuffix }}();
    });

    function openVideoModal_{{ $varSuffix }}() {
        new bootstrap.Modal(document.getElementById('{{ $modalId }}')).show();
    }

    function loadVideoFiles_{{ $varSuffix }}(append = false) {
        if (videoIsLoading_{{ $varSuffix }} || (!append && !videoHasMore_{{ $varSuffix }})) return;
        videoIsLoading_{{ $varSuffix }} = true;

        // For now, we'll simulate loading videos from server
        // In real implementation, you would call an API endpoint
        setTimeout(() => {
            const mockVideos = [{
                    url: '/uploads/video1.mp4',
                    name: 'video1.mp4'
                },
                {
                    url: '/uploads/video2.mp4',
                    name: 'video2.mp4'
                },
                {
                    url: '/uploads/video3.mp4',
                    name: 'video3.mp4'
                }
            ];

            if (append) {
                videoAllFiles_{{ $varSuffix }} = [...videoAllFiles_{{ $varSuffix }}, ...mockVideos];
            } else {
                videoAllFiles_{{ $varSuffix }} = mockVideos;
            }

            videoFilteredFiles_{{ $varSuffix }} = [...videoAllFiles_{{ $varSuffix }}];
            renderVideoFiles_{{ $varSuffix }}();

            if (mockVideos.length < videoLimit_{{ $varSuffix }}) {
                videoHasMore_{{ $varSuffix }} = false;
                document.getElementById("video-load-more-{{ $varSuffix }}").style.display = 'none';
            } else {
                document.getElementById("video-load-more-{{ $varSuffix }}").style.display = 'block';
            }

            if (append) {
                videoPage_{{ $varSuffix }}++;
            }
            videoIsLoading_{{ $varSuffix }} = false;
        }, 500);
    }

    function handleVideoFileSelect_{{ $varSuffix }}(files) {
        if (!files || files.length === 0) return;

        const videoFiles = Array.from(files).filter(file => file.type.startsWith('video/'));

        const readFileAsBase64 = (file) => new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result); // data URL (base64)
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });

        Promise.all(videoFiles.map(async (file) => {
            const dataUrl = await readFileAsBase64(file);
            // Keep for rendering list with name
            videoSelectedFiles_{{ $varSuffix }}.push({
                file: file,
                url: dataUrl,
                name: file.name
            });
            if (!videoSelectedVideos_{{ $varSuffix }}.includes(dataUrl)) {
                videoSelectedVideos_{{ $varSuffix }}.push(dataUrl);
            }
        })).then(() => {
            // Clear the file input value to allow selecting the same file again if needed
            document.getElementById('{{ $fileInputId }}').value = '';

            // Update UI immediately since we are not using modal selection
            updateVideoPreview_{{ $varSuffix }}();
            updateVideoCount_{{ $varSuffix }}();
            updateVideoInput_{{ $varSuffix }}();
        });
    }

    function filterVideoFiles_{{ $varSuffix }}(searchTerm) {
        if (!searchTerm.trim()) {
            videoFilteredFiles_{{ $varSuffix }} = [...videoAllFiles_{{ $varSuffix }}];
        } else {
            videoFilteredFiles_{{ $varSuffix }} = videoAllFiles_{{ $varSuffix }}.filter(videoData => {
                const filename = videoData.name || basename(videoData.url || videoData);
                return filename.toLowerCase().includes(searchTerm.toLowerCase());
            });
        }
        renderVideoFiles_{{ $varSuffix }}();
    }

    function renderVideoFiles_{{ $varSuffix }}() {
        const container = document.getElementById('{{ $containerId }}');

        // Combine existing videos and newly selected files
        const allVideos = [...videoFilteredFiles_{{ $varSuffix }}, ...videoSelectedFiles_{{ $varSuffix }}];

        if (allVideos.length === 0) {
            container.innerHTML = '<div class="text-center text-muted">No videos found</div>';
            return;
        }

        container.innerHTML = allVideos.map(videoData => {
            const url = videoData.url || videoData;
            const filename = videoData.name || basename(url);
            const isSelected = videoSelectedVideos_{{ $varSuffix }}.includes(url);
            const selectedIndex = videoSelectedVideos_{{ $varSuffix }}.indexOf(url) + 1;

            return `
                <div class="video-image-item ${isSelected ? 'selected' : ''}" onclick="toggleVideoSelection_{{ $varSuffix }}('${url}')">
                    <div class="video-image-card">
                        <div class="video-image-wrapper">
                            <video class="video-thumbnail" controls>
                                <source src="${url}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="video-image-actions">
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteVideoFile_{{ $varSuffix }}('${url}', '${filename}', event)" title="Delete video">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            ${isSelected ? `<div class="video-selected-badge">${selectedIndex}</div>` : ''}
                        </div>
                        <div class="video-image-info">
                            <div class="video-image-name" title="${filename}">${filename}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function toggleVideoSelection_{{ $varSuffix }}(url) {
        const index = videoSelectedVideos_{{ $varSuffix }}.indexOf(url);

        if (index > -1) {
            // Remove from selection
            videoSelectedVideos_{{ $varSuffix }}.splice(index, 1);
        } else {
            // Add to selection
            videoSelectedVideos_{{ $varSuffix }}.push(url);
        }

        renderVideoFiles_{{ $varSuffix }}();
        updateVideoModalCount_{{ $varSuffix }}();
        updateVideoActions_{{ $varSuffix }}();
    }

    function deleteVideoFile_{{ $varSuffix }}(url, filename, event) {
        event.stopPropagation();

        Swal.fire({
            title: '{{ __('are_you_sure_delete') }}',
            text: '{{ __('if_you_delete_this_video_it_cannot_be_restored') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('yes') }}',
            cancelButtonText: '{{ __('cancel') }}',
            customClass: {
                confirmButton: 'btn btn-outline-danger me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove from all arrays
                videoAllFiles_{{ $varSuffix }} = videoAllFiles_{{ $varSuffix }}.filter(video => {
                    const videoUrl = video.url || video;
                    return videoUrl !== url;
                });
                videoFilteredFiles_{{ $varSuffix }} = videoFilteredFiles_{{ $varSuffix }}.filter(
                    video => {
                        const videoUrl = video.url || video;
                        return videoUrl !== url;
                    });

                // Remove from selected files if exists
                videoSelectedFiles_{{ $varSuffix }} = videoSelectedFiles_{{ $varSuffix }}.filter(
                    video => video.url !== url);

                // Remove from selected if exists
                videoSelectedVideos_{{ $varSuffix }} = videoSelectedVideos_{{ $varSuffix }}.filter(
                    selectedUrl => selectedUrl !== url);

                renderVideoFiles_{{ $varSuffix }}();
                updateVideoPreview_{{ $varSuffix }}();
                updateVideoModalCount_{{ $varSuffix }}();
                updateVideoActions_{{ $varSuffix }}();

                window.showToastify('success', '{{ __('success_title') }}', '{{ __('success') }}', 5000);
            }
        });
    }

    function updateVideoInput_{{ $varSuffix }}() {
        const container = document.getElementById('video-hidden-files-{{ $varSuffix }}');
        container.innerHTML = ''; // Clear trước khi render lại

        videoSelectedVideos_{{ $varSuffix }}.forEach(url => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '{{ $name }}[]'; // <-- Mỗi phần tử là 1 phần tử array
            input.value = url; // Base64 hoặc URL đều ok
            container.appendChild(input);
        });
    }

    function removeSelectedVideo_{{ $varSuffix }}(url) {
        videoSelectedVideos_{{ $varSuffix }} = videoSelectedVideos_{{ $varSuffix }}.filter(selectedUrl =>
            selectedUrl !== url);
        updateVideoPreview_{{ $varSuffix }}();
        updateVideoCount_{{ $varSuffix }}();
        updateVideoInput_{{ $varSuffix }}();

        // Nếu modal đang mở, cập nhật hiển thị
        if (document.getElementById('{{ $modalId }}').classList.contains('show')) {
            renderVideoFiles_{{ $varSuffix }}();
            updateVideoModalCount_{{ $varSuffix }}();
            updateVideoActions_{{ $varSuffix }}();
        }
    }

    function clearAllSelected_{{ $varSuffix }}() {
        videoSelectedVideos_{{ $varSuffix }} = [];
        renderVideoFiles_{{ $varSuffix }}();
        updateVideoModalCount_{{ $varSuffix }}();
        updateVideoActions_{{ $varSuffix }}();
    }

    function sortSelected_{{ $varSuffix }}() {
        videoSelectedVideos_{{ $varSuffix }}.sort((a, b) => {
            const filenameA = basename(a).toLowerCase();
            const filenameB = basename(b).toLowerCase();
            return filenameA.localeCompare(filenameB);
        });
        renderVideoFiles_{{ $varSuffix }}();
    }

    function confirmVideoSelection_{{ $varSuffix }}() {
        updateVideoInput_{{ $varSuffix }}();
        updateVideoPreview_{{ $varSuffix }}();
        updateVideoCount_{{ $varSuffix }}();

        // Đóng modal
        bootstrap.Modal.getInstance(document.getElementById('{{ $modalId }}')).hide();
    }

    function updateVideoPreview_{{ $varSuffix }}() {
        const previewSection = document.getElementById('{{ $previewSectionId }}');

        if (videoSelectedVideos_{{ $varSuffix }}.length === 0) {
            previewSection.innerHTML = `
                <div class="no-video">
                    <i class="ti ti-video"></i>
                    <span>No videos selected</span>
                </div>
            `;
            return;
        }

        previewSection.innerHTML = `
            <div class="video-preview-grid">
                ${videoSelectedVideos_{{ $varSuffix }}.map((url, index) => `
                    <div class="video-preview-item" data-url="${url}">
                        <video class="video-preview-video" controls>
                            <source src="${url}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="video-preview-actions">
                            <button type="button" class="btn-remove" onclick="removeSelectedVideo_{{ $varSuffix }}('${url}')" title="Remove video">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <div class="video-preview-order">${index + 1}</div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function updateVideoCount_{{ $varSuffix }}() {
        document.getElementById('selected-count-{{ $varSuffix }}').textContent =
            videoSelectedVideos_{{ $varSuffix }}.length;
    }

    function updateVideoModalCount_{{ $varSuffix }}() {
        // This function can be used to update count in modal if needed
    }

    function updateVideoActions_{{ $varSuffix }}() {
        const actionsDiv = document.getElementById('video-actions-{{ $varSuffix }}');
        if (videoSelectedVideos_{{ $varSuffix }}.length > 0) {
            actionsDiv.style.display = 'block';
        } else {
            actionsDiv.style.display = 'none';
        }
    }

    // Utility function
    function basename(path) {
        return path.split('/').reverse()[0];
    }
</script>

<style>
    .video-selector {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
    }

    .video-preview-section {
        margin-bottom: 15px;
    }

    .video-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .video-preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .video-preview-item:hover {
        border-color: #0d6efd;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .video-preview-video {
        width: 100%;
        height: 150px;
        object-fit: cover;
        background: #000;
    }

    .video-preview-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .video-preview-item:hover .video-preview-actions {
        opacity: 1;
    }

    .btn-remove {
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-remove:hover {
        background: #dc3545;
        transform: scale(1.1);
    }

    .video-preview-order {
        position: absolute;
        bottom: 8px;
        left: 8px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .no-video {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
    }

    .no-video i {
        font-size: 48px;
        margin-bottom: 10px;
        display: block;
    }

    .video-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
    }

    .video-image-item {
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .video-image-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .video-image-item.selected {
        border: 3px solid #0d6efd;
    }

    .video-image-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .video-image-wrapper {
        position: relative;
        aspect-ratio: 16/9;
    }

    .video-thumbnail {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #000;
    }

    .video-image-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .video-image-item:hover .video-image-actions {
        opacity: 1;
    }

    .video-selected-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #0d6efd;
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .video-image-info {
        padding: 10px;
        background: #fff;
    }

    .video-image-name {
        font-size: 12px;
        color: #495057;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modal-xl {
        max-width: 1200px;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
