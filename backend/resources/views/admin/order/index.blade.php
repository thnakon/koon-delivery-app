<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Order Management') }}
            </h2>
            <div class="text-sm text-gray-500" x-data="{ seconds: 60 }" x-init="setInterval(() => { seconds--; if(seconds === 0) window.location.reload() }, 1000)">
                Auto-refresh in <span x-text="seconds"></span>s
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200 dark:border-gray-700 dark:text-gray-100">
                    
                    @if(session('success'))
                        <div class="mb-4">
                            <x-alert variant="default" title="Success" icon="lucide-check-circle-2">
                                {{ session('success') }}
                            </x-alert>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Order #</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Type / Payment</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Time</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                @forelse ($orders as $order)
                                    <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-6 py-4 font-bold text-indigo-600 whitespace-nowrap">
                                            #{{ $order->order_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium">{{ $order->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->user->phone ?? 'No phone' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <x-badge variant="{{ $order->type === 'delivery' ? 'default' : 'secondary' }}">
                                                    {{ ucfirst($order->type) }}
                                                </x-badge>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ ucfirst($order->payment_method) }} 
                                                ({{ $order->payment_status }})
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-bold whitespace-nowrap">
                                            {{ number_format($order->total, 2) }} ฿
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $order->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
                                            <x-button as="a" href="{{ route('admin.order.show', $order) }}" variant="outline" size="sm">
                                                Manage
                                            </x-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
