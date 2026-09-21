<article class="product-card">
    <a href="{{ route('menu.show', $item->id) }}" class="product-media-link">
        @if($item->category)<span class="product-card-badge">{{ $item->category }}</span>@endif
        <x-menu-item-image :menu-item="$item" alt="{{ $item->name }} - Kaleni Catering Services menu" />
    </a>
    <div class="product-info">
        <div class="product-name-price">
            <a href="{{ route('menu.show', $item->id) }}" class="product-name-link">{{ $item->name }}</a>
            <div class="price-row">
                <span class="price-dots" aria-hidden="true"></span>
                <span class="product-price">N$ {{ number_format($item->price, 2) }}</span>
            </div>
        </div>
        @if($item->stock > 0)
            <form action="{{ route('cart.add') }}" method="POST" data-add-to-cart>
                @csrf
                <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="buy-now-btn"><i class="bi bi-bag-plus" aria-hidden="true"></i> Add to order</button>
            </form>
        @else
            <button class="buy-now-btn" disabled>Out of stock</button>
        @endif
    </div>
</article>
