<?php

namespace App\Http\Controllers\AdminWeb;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.order.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.menuItem', 'coupon']);
        return view('admin.order.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivering,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        // Try broadcasting if the event exists
        if (class_exists(OrderStatusUpdated::class)) {
            broadcast(new OrderStatusUpdated($order))->toOthers();
        }

        return redirect()->back()->with('success', 'Order status updated to ' . ucfirst($validated['status']));
    }
}
