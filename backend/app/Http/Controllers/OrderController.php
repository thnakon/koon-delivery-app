<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:pickup,delivery',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
            'coupon_code' => 'nullable|string',
            'note' => 'nullable|string',
            'delivery_address' => 'required_if:type,delivery|string',
            'delivery_lat' => 'required_if:type,delivery|numeric',
            'delivery_lng' => 'required_if:type,delivery|numeric',
            'idempotency_key' => 'required|string',
        ]);

        // Check idempotency key to prevent duplicate orders
        $existingOrder = Order::where('idempotency_key', $validated['idempotency_key'])->first();
        if ($existingOrder) {
            return response()->json($existingOrder->load('items.menuItem'), 200);
        }

        return DB::transaction(function () use ($validated, $request) {
            $subtotal = 0;
            $orderItemsData = [];

            // Calculate Server-Side Pricing
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                
                if (!$menuItem->is_available) {
                    abort(400, "Item {$menuItem->name} is not available.");
                }

                $itemTotal = $menuItem->price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'total_price' => $itemTotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ];
            }

            $discount = 0;
            $couponId = null;

            // Coupon Logic
            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::where('code', $validated['coupon_code'])
                                ->where('is_active', true)
                                ->first();

                if ($coupon && (!$coupon->expires_at || $coupon->expires_at > now()) &&
                    (!$coupon->starts_at || $coupon->starts_at <= now())) {
                    
                    if (!$coupon->min_order_amount || $subtotal >= $coupon->min_order_amount) {
                        if ($coupon->type->value === 'fixed') {
                            $discount = $coupon->value;
                        } else {
                            $discount = $subtotal * ($coupon->value / 100);
                        }

                        if ($coupon->max_discount && $discount > $coupon->max_discount) {
                            $discount = $coupon->max_discount;
                        }

                        $couponId = $coupon->id;
                        $coupon->increment('used_count');
                    }
                }
            }

            $deliveryFee = $validated['type'] === 'delivery' ? 50.00 : 0; // Flat 50 THB fee for MVP
            $total = max(0, $subtotal - $discount) + $deliveryFee;

            // Generate Order Number
            $orderNumber = 'KN-' . strtoupper(Str::random(8));

            // Determine queue position
            $queuePosition = Order::whereDate('created_at', today())->count() + 1;

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $request->user()->id,
                'coupon_id' => $couponId,
                'type' => $validated['type'],
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'note' => $validated['note'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_lat' => $validated['delivery_lat'] ?? null,
                'delivery_lng' => $validated['delivery_lng'] ?? null,
                'queue_position' => $queuePosition,
                'idempotency_key' => $validated['idempotency_key'],
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            return response()->json($order->load('items.menuItem'), 201);
        });
    }

    public function show(Order $order, Request $request): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json($order->load('items.menuItem', 'coupon'));
    }
}
