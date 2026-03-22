<?php

namespace App\Http\Controllers\AdminWeb;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with('category')->latest()->paginate(10);
        return view('admin.menu.index', compact('menuItems'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $validated['is_popular'] = $request->has('is_popular');
        $validated['prep_time_minutes'] = $validated['prep_time_minutes'] ?? 0;

        $menuItem = MenuItem::create($validated);

        if ($request->hasFile('image')) {
            $menuItem->addMediaFromRequest('image')->toMediaCollection('images');
            $menuItem->update(['image_url' => $menuItem->getFirstMediaUrl('images')]);
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menu)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'nullable',
            'is_popular' => 'nullable',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $validated['is_popular'] = $request->has('is_popular');
        $validated['prep_time_minutes'] = $validated['prep_time_minutes'] ?? 0;

        $menu->update($validated);

        if ($request->hasFile('image')) {
            $menu->clearMediaCollection('images');
            $menu->addMediaFromRequest('image')->toMediaCollection('images');
            $menu->update(['image_url' => $menu->getFirstMediaUrl('images')]);
        }

        return redirect()->route('admin.menu.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu item deleted successfully.');
    }
}
