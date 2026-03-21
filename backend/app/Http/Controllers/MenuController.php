<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    public function index(): JsonResponse
    {
        $items = MenuItem::with('category')
            ->where('is_available', true)
            ->get()
            ->groupBy('category_id');

        return response()->json($items);
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        $menuItem->load('category');
        return response()->json($menuItem);
    }
}
