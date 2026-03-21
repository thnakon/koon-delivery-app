<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(MenuItem::with('category')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'is_popular' => 'boolean',
            'prep_time_minutes' => 'integer|min:0',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $menuItem = MenuItem::create($validated);

        if ($request->hasFile('image')) {
            $menuItem->addMediaFromRequest('image')->toMediaCollection('images');
            $menuItem->update(['image_url' => $menuItem->getFirstMediaUrl('images')]);
        }

        return response()->json($menuItem->load('category'), 201);
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        return response()->json($menuItem->load('category'));
    }

    public function update(Request $request, MenuItem $menuItem): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'is_available' => 'boolean',
            'is_popular' => 'boolean',
            'prep_time_minutes' => 'integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $menuItem->update($validated);

        if ($request->hasFile('image')) {
            $menuItem->clearMediaCollection('images');
            $menuItem->addMediaFromRequest('image')->toMediaCollection('images');
            $menuItem->update(['image_url' => $menuItem->getFirstMediaUrl('images')]);
        }

        return response()->json($menuItem->load('category'));
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $menuItem->delete();
        return response()->json(null, 204);
    }
}
