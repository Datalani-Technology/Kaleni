<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodOfTheDay;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodOfTheDayController extends Controller
{
    public function index()
    {
        $schedule = FoodOfTheDay::with('menuItem')
            ->orderByDesc('serve_date')
            ->paginate(20);

        return view('admin.food-of-the-day.index', compact('schedule'));
    }

    public function create()
    {
        $menuItems = MenuItem::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']);

        return view('admin.food-of-the-day.create', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('food-of-the-day', 'public');
        }

        FoodOfTheDay::create($validated);

        return redirect()->route('admin.food-of-the-day.index')->with('success', 'Food of the Day scheduled.');
    }

    public function edit(FoodOfTheDay $foodOfTheDay)
    {
        $menuItems = MenuItem::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']);

        return view('admin.food-of-the-day.edit', ['entry' => $foodOfTheDay, 'menuItems' => $menuItems]);
    }

    public function update(Request $request, FoodOfTheDay $foodOfTheDay)
    {
        $validated = $this->validated($request, $foodOfTheDay->id);

        if ($request->hasFile('image')) {
            if ($foodOfTheDay->image) {
                Storage::disk('public')->delete($foodOfTheDay->image);
            }
            $validated['image'] = $request->file('image')->store('food-of-the-day', 'public');
        }

        $foodOfTheDay->update($validated);

        return redirect()->route('admin.food-of-the-day.index')->with('success', 'Food of the Day updated.');
    }

    public function destroy(FoodOfTheDay $foodOfTheDay)
    {
        if ($foodOfTheDay->image) {
            Storage::disk('public')->delete($foodOfTheDay->image);
        }
        $foodOfTheDay->delete();

        return redirect()->route('admin.food-of-the-day.index')->with('success', 'Removed from the schedule.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'menu_item_id' => 'nullable|exists:menu_items,id',
            'serve_date' => 'required|date|unique:food_of_the_day,serve_date' . ($ignoreId ? ",{$ignoreId}" : ''),
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['menu_item_id']) && empty($validated['title'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'title' => 'Pick a menu item or give the special its own title.',
            ]);
        }

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
