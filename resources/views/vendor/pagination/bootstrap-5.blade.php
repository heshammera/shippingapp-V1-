@if ($paginator->hasPages())
    <nav class="d-flex justify-items-center justify-content-between">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled animate__animated animate__fadeIn" aria-disabled="true">
                        <span class="page-link"><i class="bi bi-arrow-left-short"></i> السابق</span>
                    </li>
                @else
                    <li class="page-item animate__animated animate__fadeIn">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="bi bi-arrow-left-short"></i> السابق
                        </a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item animate__animated animate__fadeIn">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            التالي <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled animate__animated animate__fadeIn" aria-disabled="true">
                        <span class="page-link">التالي <i class="bi bi-arrow-right-short"></i></span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-muted m-0">
                    عرض
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    إلى
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    من
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                    نتيجة
                </p>
            </div>

            <div>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled animate__animated animate__fadeIn" aria-disabled="true" aria-label="السابق">
                            <span class="page-link" aria-hidden="true"><i class="bi bi-arrow-left-short"></i> السابق</span>
                        </li>
                    @else
                        <li class="page-item animate__animated animate__fadeIn">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="السابق">
                                <i class="bi bi-arrow-left-short"></i> السابق
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled animate__animated animate__fadeIn" aria-disabled="true">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active animate__animated animate__fadeIn" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item animate__animated animate__fadeIn">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item animate__animated animate__fadeIn">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="التالي">
                                التالي <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled animate__animated animate__fadeIn" aria-disabled="true" aria-label="التالي">
                            <span class="page-link" aria-hidden="true">التالي <i class="bi bi-arrow-right-short"></i></span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
