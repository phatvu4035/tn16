@if ($paginator->hasPages())
    <nav aria-label="">
        {{--<p class="pull-left">Có tất cả {{$paginator->total()}} bản ghi</p>--}}
        <ul class="pagination justify-content-center">
            @if ($paginator->currentPage() > 1)
                <li class="page-item"><a class="page-link"  data-page="1" href="javascript:void(0)">&larr;</a></li>
            @else
                <li class="page-item disabled"><a class="page-link"
                                                  href="{{ $paginator->previousPageUrl() }}">&larr;</a></li>

            @endif

            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled page-item"><a class="page-link">{{ $element }}</a></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item disabled"><a class="page-link" href="javascript:void(0)">{{ $page }}</a></li>
                        @else
                            <li class="page-item"><a class="page-link" data-page="{{$page}}" data-href="{{ $url }}" href="javascript:void(0)">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->currentPage() < $paginator->lastPage())
                <li class="page-item"><a class="page-link" data-page="{{ $paginator->lastPage() }}" href="javascript:void(0)">&rarr;</a></li>
            @else
                <li class="page-item disabled"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">&rarr;</a>
                </li>
            @endif
        </ul>
    </nav>
@endif