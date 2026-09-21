<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::withCount('menuItems')->latest()->paginate(20);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    public function create()
    {
        return view('admin.promo-codes.create', [
            'menuItems' => MenuItem::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $promoCode = PromoCode::create($validated);

        if ($validated['scope'] === 'products') {
            $promoCode->menuItems()->sync($request->input('menu_item_ids', []));
        }

        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code created.');
    }

    public function edit(PromoCode $promoCode)
    {
        $promoCode->load('menuItems:id');

        return view('admin.promo-codes.edit', [
            'promoCode' => $promoCode,
            'menuItems' => MenuItem::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $this->validated($request, $promoCode->id);

        $promoCode->update($validated);

        $promoCode->menuItems()->sync($validated['scope'] === 'products' ? $request->input('menu_item_ids', []) : []);

        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code updated.');
    }

    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')->with('success', 'Promo code deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code' . ($ignoreId ? ",{$ignoreId}" : ''),
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0.01',
            'scope' => 'required|in:all,products',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
            'menu_item_ids' => 'nullable|array',
            'menu_item_ids.*' => 'exists:menu_items,id',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        if ($validated['type'] === 'percent' && $validated['value'] > 100) {
            $validated['value'] = 100;
        }

        return $validated;
    }
}
