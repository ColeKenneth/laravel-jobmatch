<div class="directory-pagination">
    @if($items->hasMorePages())
        <a class="button" href="{{ $items->nextPageUrl() }}">Show More {{ $label }}</a>
    @endif
    <div class="pagination">{{ $items->onEachSide(1)->links() }}</div>
</div>
