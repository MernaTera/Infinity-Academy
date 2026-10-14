@if ($paginator->hasPages())
@once
<style>
.inf-pager{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:18px;font-family:'DM Sans',sans-serif;}
.inf-pager-info{font-size:11px;letter-spacing:0.5px;color:#5A6A85;}
.inf-pager-info strong{color:#16233F;font-weight:600;}
.inf-pager-list{display:flex;align-items:center;gap:6px;flex-wrap:wrap;list-style:none;margin:0;padding:0;}
.inf-pager-btn{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:10px;font-size:12px;font-weight:600;color:#5A6A85;text-decoration:none;background:rgba(255,255,255,0.55);-webkit-backdrop-filter:blur(12px) saturate(150%);backdrop-filter:blur(12px) saturate(150%);border:1px solid rgba(27,79,168,0.12);box-shadow:0 4px 12px rgba(23,45,90,0.05);transition:all 0.18s;}
a.inf-pager-btn:hover{color:#1B4FA8;border-color:rgba(27,79,168,0.3);background:rgba(27,79,168,0.06);text-decoration:none;transform:translateY(-1px);}
.inf-pager-btn.is-active{background:linear-gradient(120deg,#1B4FA8,#2D6FDB);border-color:transparent;color:#fff;box-shadow:0 8px 20px rgba(27,79,168,0.28);}
.inf-pager-btn.is-disabled{opacity:0.4;cursor:not-allowed;}
.inf-pager-btn.is-dots{background:transparent;border-color:transparent;box-shadow:none;color:#93A3BC;min-width:20px;padding:0 2px;}
.inf-pager-btn svg{display:block;}
@media(max-width:600px){.inf-pager{justify-content:center;}.inf-pager-info{width:100%;text-align:center;}}
</style>
@endonce
<nav class="inf-pager" role="navigation" aria-label="Pagination">
    <div class="inf-pager-info">
        Showing <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
        of <strong>{{ $paginator->total() }}</strong>
    </div>

    <ul class="inf-pager-list">
        @if ($paginator->onFirstPage())
            <li><span class="inf-pager-btn is-disabled" aria-disabled="true">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </span></li>
        @else
            <li><a class="inf-pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </a></li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span class="inf-pager-btn is-dots">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="inf-pager-btn is-active" aria-current="page">{{ $page }}</span></li>
                    @else
                        <li><a class="inf-pager-btn" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li><a class="inf-pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a></li>
        @else
            <li><span class="inf-pager-btn is-disabled" aria-disabled="true">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </span></li>
        @endif
    </ul>
</nav>
@endif