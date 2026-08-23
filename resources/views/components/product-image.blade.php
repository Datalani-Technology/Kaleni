@props(['product', 'imgClass' => 'product-image', 'alt' => null])
@php
    $alt = $alt ?? ($product->name . ' - /Namsa Florals');
@endphp
@if($product->image)
    <img src="{{ $product->image_url }}" class="{{ $imgClass }}" alt="{{ $alt }}" title="{{ $product->name }}" loading="lazy">
@else
    <div class="{{ $imgClass }} product-image-placeholder" role="img" aria-label="{{ $alt }}">
        <i class="bi bi-flower1"></i>
    </div>
@endif
