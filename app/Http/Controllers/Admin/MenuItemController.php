<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::latest()->paginate(15);
        return view('admin.menu-items.index', compact('menuItems'));
    }

    public function export(): StreamedResponse
    {
        $menuItems = MenuItem::orderBy('name')->get();

        return response()->streamDownload(function () use ($menuItems) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Category', 'Price (N$)', 'Unit', 'Serves', 'Stock', 'Active', 'Featured']);
            foreach ($menuItems as $item) {
                fputcsv($out, [
                    $item->name,
                    $item->category,
                    number_format((float) $item->price, 2, '.', ''),
                    $item->unit_label,
                    $item->serves_count,
                    $item->stock,
                    $item->is_active ? 'Yes' : 'No',
                    $item->is_featured ? 'Yes' : 'No',
                ]);
            }
            fclose($out);
        }, 'menu-items-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function create()
    {
        return view('admin.menu-items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit_label' => 'nullable|string|max:50',
            'serves_count' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        MenuItem::create($validated);

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item created successfully.');
    }

    public function show(MenuItem $menuItem)
    {
        return view('admin.menu-items.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        return view('admin.menu-items.edit', compact('menuItem'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit_label' => 'nullable|string|max:50',
            'serves_count' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($menuItem->hasManagedImage()) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $menuItem->update($validated);

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->hasManagedImage()) {
            Storage::disk('public')->delete($menuItem->image);
        }
        $menuItem->delete();

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:menu_items,id',
        ], ['ids.required' => 'Select at least one menu item to delete.', 'ids.min' => 'Select at least one menu item to delete.']);

        $count = 0;
        foreach ($validated['ids'] as $id) {
            $menuItem = MenuItem::find($id);
            if ($menuItem) {
                if ($menuItem->hasManagedImage() && Storage::disk('public')->exists($menuItem->image)) {
                    Storage::disk('public')->delete($menuItem->image);
                }
                $menuItem->delete();
                $count++;
            }
        }

        return redirect()->route('admin.menu-items.index')
            ->with('success', $count === 1 ? '1 menu item deleted.' : $count . ' menu items deleted.');
    }
}
