<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.order.index') }}" class="text-gray-500 hover:text-gray-700">
                    &larr; Back to Orders
                </a>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Order #') }}{{ $order->order_number }}
                </h2>
            </div>
            
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'preparing' => 'bg-orange-100 text-orange-800',
                    'ready' => 'bg-green-100 text-green-800',
                    'delivering' => 'bg-indigo-100 text-indigo-800',
                    'completed' => 'bg-gray-100 text-gray-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="grid grid-cols-1 gap-6 mx-auto max-w-7xl sm:px-6 lg:px-8 md:grid-cols-3">
            
            <!-- Left Column: Order Items & Customer Details -->
            <div class="space-y-6 md:col-span-2">
                <!-- Order Items -->
                <x-card>
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Order Items</h3>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between py-4">
                                    <div class="flex items-center space-x-4">
                                        @if($item->menuItem->image_url)
                                            <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="object-cover w-12 h-12 rounded">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded dark:bg-gray-700"></div>
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $item->menuItem->name }}</div>
                                            @if($item->special_instructions)
                                                <div class="mt-1 text-sm text-yellow-600">Note: {{ $item->special_instructions }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ number_format($item->unit_price, 2) }} ฿ x {{ $item->quantity }}</div>
                                        <div class="text-sm font-bold text-gray-500">{{ number_format($item->total_price, 2) }} ฿</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-card>

                <!-- Customer & Delivery -->
                <x-card>
                    <div class="grid grid-cols-2 gap-6 p-6">
                        <div>
                            <h3 class="mb-2 text-sm font-medium tracking-wider text-gray-500 uppercase">Customer Details</h3>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $order->user->name }}</div>
                            <div class="text-gray-500">{{ $order->user->email }}</div>
                            <div class="text-gray-500">{{ $order->user->phone ?? 'No phone number' }}</div>
                        </div>
                        <div>
                            <h3 class="mb-2 text-sm font-medium tracking-wider text-gray-500 uppercase">Delivery Details</h3>
                            <div class="font-medium text-gray-900 dark:text-white">Type: {{ ucfirst($order->type) }}</div>
                            @if($order->type === 'delivery')
                                <div class="mt-1 text-gray-500">{{ $order->delivery_address }}</div>
                            @endif
                            @if($order->note)
                                <div class="p-3 mt-2 text-sm text-yellow-800 rounded-md bg-yellow-50">
                                    <strong>Note:</strong> {{ $order->note }}
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Right Column: Status Update & Summary -->
            <div class="space-y-6">
                <!-- Update Status Form -->
                <x-card>
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Update Status</h3>
                        
                        @if(session('success'))
                            <div class="mb-4">
                                <x-alert variant="default" title="Success" icon="lucide-check-circle-2">
                                    {{ session('success') }}
                                </x-alert>
                            </div>
                        @endif

                        <form action="{{ route('admin.order.status', $order) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            @php
                                $statuses = [
                                    'pending' => 'Pending (New)',
                                    'confirmed' => 'Confirmed (Accepted)',
                                    'preparing' => 'Preparing',
                                    'ready' => 'Ready (For Pickup / Delivery)',
                                    'delivering' => 'Delivering',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ];
                            @endphp
                            
                            <div class="space-y-2">
                                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" required>
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <x-button type="submit" class="w-full justify-center">Update Status</x-button>
                        </form>
                    </div>
                </x-card>

                <!-- Order Summary -->
                <x-card>
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Payment Summary</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>{{ number_format($order->subtotal, 2) }} ฿</span>
                            </div>
                            
                            @if($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount {!! $order->coupon ? '<br><small>('.$order->coupon->code.')</small>' : '' !!}</span>
                                <span>-{{ number_format($order->discount, 2) }} ฿</span>
                            </div>
                            @endif

                            @if($order->type === 'delivery')
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Delivery Fee</span>
                                <span>{{ number_format($order->delivery_fee, 2) }} ฿</span>
                            </div>
                            @endif

                            <div class="flex justify-between pt-3 text-base font-bold text-gray-900 border-t border-gray-200 dark:border-gray-700 dark:text-white">
                                <span>Total</span>
                                <span>{{ number_format($order->total, 2) }} ฿</span>
                            </div>
                        </div>

                        <div class="pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="mb-1 text-xs tracking-wider text-gray-500 uppercase">Payment Method</div>
                            <div class="flex items-center justify-between font-medium text-gray-900 dark:text-white">
                                <span>{{ ucfirst($order->payment_method) }}</span>
                                <x-badge variant="{{ $order->payment_status === 'paid' ? 'default' : 'secondary' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </x-badge>
                            </div>
                            @if($order->omise_charge_id)
                                <div class="mt-1 text-xs text-gray-400">Charge ID: {{ $order->omise_charge_id }}</div>
                            @endif
                        </div>
                    </div>
                </x-card>
            </div>
            
        </div>
    </div>
</x-app-layout>
