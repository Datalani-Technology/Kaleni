@php
    $promoCode = $promoCode ?? null;
    $selectedProductIds = old('product_ids', $promoCode?->products->pluck('id')->toArray() ?? []);
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="code" class="form-label">Code *</label>
        <input type="text" class="form-control text-uppercase @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $promoCode?->code) }}" maxlength="50" placeholder="e.g. SPRING20" required {{ $promoCode ? '' : 'autofocus' }}>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="description" class="form-label">Description <span class="optional-label text-muted">Optional, internal note</span></label>
        <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $promoCode?->description) }}" maxlength="255">
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="type" class="form-label">Discount type *</label>
        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
            <option value="percent" {{ old('type', $promoCode?->type) === 'percent' ? 'selected' : '' }}>Percent off</option>
            <option value="fixed" {{ old('type', $promoCode?->type) === 'fixed' ? 'selected' : '' }}>Fixed amount off (N$)</option>
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="value" class="form-label">Value *</label>
        <input type="number" step="0.01" min="0.01" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $promoCode?->value) }}" required>
        @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="max_discount_amount" class="form-label">Max discount (N$) <span class="optional-label text-muted">Optional cap</span></label>
        <input type="number" step="0.01" min="0" class="form-control @error('max_discount_amount') is-invalid @enderror" id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', $promoCode?->max_discount_amount) }}">
        @error('max_discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="min_order_amount" class="form-label">Minimum order (N$) <span class="optional-label text-muted">Optional</span></label>
        <input type="number" step="0.01" min="0" class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', $promoCode?->min_order_amount) }}">
        @error('min_order_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="usage_limit" class="form-label">Usage limit <span class="optional-label text-muted">Optional, total redemptions</span></label>
        <input type="number" min="1" class="form-control @error('usage_limit') is-invalid @enderror" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $promoCode?->usage_limit) }}">
        @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="starts_at" class="form-label">Starts <span class="optional-label text-muted">Optional</span></label>
        <input type="date" class="form-control @error('starts_at') is-invalid @enderror" id="starts_at" name="starts_at" value="{{ old('starts_at', optional($promoCode?->starts_at)->toDateString()) }}">
        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="ends_at" class="form-label">Ends <span class="optional-label text-muted">Optional</span></label>
        <input type="date" class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at" value="{{ old('ends_at', optional($promoCode?->ends_at)->toDateString()) }}">
        @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label d-block">Applies to *</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="scope" id="scope_all" value="all" {{ old('scope', $promoCode?->scope ?? 'all') === 'all' ? 'checked' : '' }} onchange="document.getElementById('promoProductPicker').classList.add('d-none')">
            <label class="form-check-label" for="scope_all">All products</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="scope" id="scope_products" value="products" {{ old('scope', $promoCode?->scope) === 'products' ? 'checked' : '' }} onchange="document.getElementById('promoProductPicker').classList.remove('d-none')">
            <label class="form-check-label" for="scope_products">Specific products</label>
        </div>
    </div>
    <div class="col-12 {{ old('scope', $promoCode?->scope) === 'products' ? '' : 'd-none' }}" id="promoProductPicker">
        <label class="form-label">Eligible products</label>
        <select class="form-select @error('product_ids') is-invalid @enderror" name="product_ids[]" multiple size="8">
            @foreach($products as $product)
                <option value="{{ $product->id }}" {{ in_array($product->id, $selectedProductIds) ? 'selected' : '' }}>{{ $product->name }}</option>
            @endforeach
        </select>
        <div class="form-text">Ctrl/Cmd-click to select multiple.</div>
        @error('product_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $promoCode?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
    <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
