<div class="container">
    <div class="row align-items-center justify-content-center">
        <!-- Pagination Controls -->
        <div class="col-auto">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($fechtedData->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">«</span></li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $fechtedData->previousPageUrl() }}&limit={{ request('limit') }}"
                            rel="prev">«</a>
                    </li>
                @endif
                {{-- Pagination Elements --}}
                @foreach ($fechtedData->linkCollection() as $link)
                    @if (!str_contains($link['label'], 'Previous') && !str_contains($link['label'], 'Next'))
                        <li class="page-item {{ $link['active'] ? 'active' : '' }}">
                            @if ($link['url'])
                                @php
                                    $separator = parse_url($link['url'], PHP_URL_QUERY) ? '&' : '?';
                                @endphp
                                <a class="page-link"
                                    href="{{ $link['url'] }}{{ $separator }}limit={{ request('limit', 10) }}">{{ $link['label'] }}</a>
                            @else
                                <span class="page-link">{{ $link['label'] }}</span>
                            @endif
                        </li>
                    @endif
                @endforeach
                {{-- Next Page Link --}}
                @if ($fechtedData->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $fechtedData->nextPageUrl() }}&limit={{ request('limit') }}"
                            rel="next">»</a>
                    </li>
                @else
                    <li class="page-item disabled"><span class="page-link">»</span></li>
                @endif
            </ul>
        </div>
        <!-- Select Limit Dropdown -->
        <div class="col-auto">
            <form action="{{ request()->url() }}" method="GET">
                <select name="limit" class="form-select" onchange="this.form.submit()">
                    <option value="5" {{ request('limit', 10) == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('limit', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('limit', 10) == 15 ? 'selected' : '' }}>15</option>
                    <option value="20" {{ request('limit', 10) == 20 ? 'selected' : '' }}>20</option>
                </select>
            </form>
        </div>
    </div>
</div>
