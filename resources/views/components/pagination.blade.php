@if ($paginator->hasPages())
    @php($elements = \Illuminate\Pagination\UrlWindow::make($paginator))
    <nav class="app-pagination" aria-label="Paginación">
        <div class="app-pagination__summary">
            Mostrando <strong>{{ $paginator->firstItem() }}</strong> a <strong>{{ $paginator->lastItem() }}</strong> de <strong>{{ $paginator->total() }}</strong> resultados
        </div>

        <ul class="app-pagination__links">
            @if ($paginator->onFirstPage())
                <li><span class="app-pagination__link app-pagination__link--disabled" aria-disabled="true"><i class="fas fa-chevron-left"></i><span class="visually-hidden">Anterior</span></span></li>
            @else
                <li><a class="app-pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior"><i class="fas fa-chevron-left"></i></a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="app-pagination__ellipsis">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="app-pagination__link app-pagination__link--active" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a class="app-pagination__link" href="{{ $url }}" aria-label="Ir a la página {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a class="app-pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Página siguiente"><i class="fas fa-chevron-right"></i></a></li>
            @else
                <li><span class="app-pagination__link app-pagination__link--disabled" aria-disabled="true"><i class="fas fa-chevron-right"></i><span class="visually-hidden">Siguiente</span></span></li>
            @endif
        </ul>
    </nav>
@endif
