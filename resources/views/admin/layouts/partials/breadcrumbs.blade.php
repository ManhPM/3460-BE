@if (isset($breadcrumbs) && !empty($breadcrumbs->getBreadcrumbs()))
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav class="fancy-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb-list">
                            @foreach ($breadcrumbs = $breadcrumbs->getBreadcrumbs() as $item)
                                @if ($loop->first)
                                    <li class="breadcrumb-item">
                                        @if ($item['url'])
                                            <a href="{{ $item['url'] }}" class="breadcrumb-link">
                                                <span class="breadcrumb-icon">
                                                    🏠
                                                </span>
                                                <span class="breadcrumb-text">{{ $item['label'] }}</span>
                                            </a>
                                        @else
                                            <span class="breadcrumb-link">
                                                <span class="breadcrumb-icon">
                                                    🏠
                                                </span>
                                                <span class="breadcrumb-text">{{ $item['label'] }}</span>
                                            </span>
                                        @endif
                                    </li>
                                @elseif ($loop->last)
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <span class="breadcrumb-link">
                                            <span class="breadcrumb-icon">📍</span>
                                            <span class="breadcrumb-text">{{ $item['label'] }}</span>
                                        </span>
                                    </li>
                                @else
                                    <li class="breadcrumb-item">
                                        @if ($item['url'])
                                            <a href="{{ $item['url'] }}" class="breadcrumb-link">
                                                <span class="breadcrumb-icon">
                                                    🛣️
                                                </span>
                                                <span class="breadcrumb-text">{{ $item['label'] }}</span>
                                            </a>
                                        @else
                                            <span class="breadcrumb-link">
                                                <span class="breadcrumb-icon">
                                                    🛣️
                                                </span>
                                                <span class="breadcrumb-text">{{ $item['label'] }}</span>
                                            </span>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endif
