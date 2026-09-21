@props(['menuItem', 'imgClass' => 'product-image', 'alt' => null])
@php
    $alt = $alt ?? ($menuItem->name . ' - Kaleni Catering Services');
@endphp
@if($menuItem->image)
    <img src="{{ $menuItem->image_url }}" class="{{ $imgClass }}" alt="{{ $alt }}" title="{{ $menuItem->name }}" loading="lazy">
@else
    <div class="{{ $imgClass }} product-image-placeholder" role="img" aria-label="{{ $alt }}">
        <i class="bi bi-egg-fried"></i>
    </div>
@endif
