@props(['menuItem', 'size' => 50, 'alt' => ''])
@php
    $size = (int) $size;
    $alt = $alt ?: ($menuItem->name ?? '');
@endphp
@if($menuItem->image ?? null)
    <span class="admin-product-img-wrap d-inline-block">
        <img src="{{ $menuItem->image_url }}"
             alt="{{ $alt }}"
             width="{{ $size }}"
             height="{{ $size }}"
             loading="eager"
             style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover; border-radius: 5px; vertical-align: middle;"
             onerror="this.onerror=null; this.style.display='none'; var p=this.nextElementSibling; if(p) p.style.display='inline-block';">
        <span class="admin-product-img-placeholder" style="display: none; width: {{ $size }}px; height: {{ $size }}px; background: #e9ecef; border-radius: 5px; vertical-align: middle;" aria-hidden="true"></span>
    </span>
@else
    <span class="admin-product-img-placeholder" style="display: inline-block; width: {{ $size }}px; height: {{ $size }}px; background: #e9ecef; border-radius: 5px; vertical-align: middle;" aria-hidden="true"></span>
@endif
