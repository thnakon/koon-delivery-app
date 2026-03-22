<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Coupon Management') }}
            </h2>
            <x-button as="a" href="{{ route('admin.coupon.create') }}">
                Add Coupon
            </x-button>
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
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Code</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Type / Value</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Usage</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Conditions</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                @forelse ($coupons as $coupon)
                                    <tr>
                                        <td class="px-6 py-4 font-bold text-indigo-600 whitespace-nowrap">{{ $coupon->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($coupon->type === 'fixed')
                                                {{ number_format($coupon->value, 2) }} ฿
                                            @else
                                                {{ number_format($coupon->value, 0) }}% 
                                                <span class="text-xs text-gray-500">(Max: {{ number_format($coupon->max_discount, 2) }} ฿)</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $coupon->used_count }} / {{ $coupon->usage_limit ?: '∞' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            Min: {{ number_format($coupon->min_order_amount, 2) }} ฿
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-badge variant="{{ $coupon->is_active && (!$coupon->expires_at || $coupon->expires_at > now()) ? 'default' : 'secondary' }}">
                                                {{ $coupon->is_active ? ($coupon->expires_at && $coupon->expires_at < now() ? 'Expired' : 'Active') : 'Inactive' }}
                                            </x-badge>
                                        </td>
                                        <td class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
                                            <x-link href="{{ route('admin.coupon.edit', $coupon) }}">Edit</x-link>
                                            <form action="{{ route('admin.coupon.destroy', $coupon) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="destructive" size="sm" type="submit">Delete</x-button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No coupons found. Create your first coupon!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $coupons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
