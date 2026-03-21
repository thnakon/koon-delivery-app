<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('user', 'items.menuItem')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load('user', 'items.menuItem', 'coupon'));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivering,completed,cancelled',
            'estimated_ready_at' => 'nullable|date',
        ]);

        $order->update($validated);

        \App\Events\OrderStatusUpdated::dispatch($order);
        
        return response()->json($order);
    }
}
