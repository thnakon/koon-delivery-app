<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validateCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $validated['code'])
                        ->where('is_active', true)
                        ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon code.'], 400);
        }

        if ($coupon->expires_at && $coupon->expires_at < now()) {
            return response()->json(['message' => 'Coupon has expired.'], 400);
        }

        if ($coupon->starts_at && $coupon->starts_at > now()) {
            return response()->json(['message' => 'Coupon is not yet active.'], 400);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['message' => 'Coupon usage limit reached.'], 400);
        }

        if ($coupon->min_order_amount && $validated['subtotal'] < $coupon->min_order_amount) {
            return response()->json([
                'message' => "Minimum order amount of {$coupon->min_order_amount} required."
            ], 400);
        }

        $discount = 0;
        if ($coupon->type->value === 'fixed') {
            $discount = $coupon->value;
        } else {
            $discount = $validated['subtotal'] * ($coupon->value / 100);
        }

        if ($coupon->max_discount && $discount > $coupon->max_discount) {
            $discount = $coupon->max_discount;
        }

        return response()->json([
            'coupon' => $coupon,
            'discount' => round($discount, 2),
        ]);
    }
}
