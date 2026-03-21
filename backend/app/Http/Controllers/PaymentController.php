<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function charge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'omise_token' => 'required|string',
        ]);

        // Placeholder for Omise integration
        // In real implementation:
        // 1. Fetch order
        // 2. Validate it's pending payment
        // 3. Call Omise API `OmiseCharge::create([...])`
        // 4. Update order payment_status to 'paid'

        return response()->json([
            'message' => 'Payment processor simulated successfully.',
            'status' => 'successful'
        ]);
    }
}
