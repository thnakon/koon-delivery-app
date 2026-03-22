<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Edit Coupon: ') }} {{ $coupon->code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-card>
                <div class="p-6">
                    <form action="{{ route('admin.coupon.update', $coupon) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Code -->
                        <div class="space-y-2">
                            <x-label for="code">Coupon Code</x-label>
                            <x-input id="code" name="code" type="text" value="{{ old('code', $coupon->code) }}" required autofocus class="uppercase" />
                            @error('code') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <!-- Type & Value -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <x-label for="type">Discount Type</x-label>
                                <select id="type" name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" required>
                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (฿)</option>
                                </select>
                                @error('type') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <x-label for="value">Discount Value</x-label>
                                <x-input id="value" name="value" type="number" step="0.01" min="0" value="{{ old('value', (float)$coupon->value) }}" required />
                                @error('value') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Min Order & Max Discount -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <x-label for="min_order_amount">Minimum Order Amount (฿)</x-label>
                                <x-input id="min_order_amount" name="min_order_amount" type="number" step="0.01" min="0" value="{{ old('min_order_amount', $coupon->min_order_amount ? (float)$coupon->min_order_amount : '') }}" />
                                @error('min_order_amount') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <x-label for="max_discount">Maximum Discount (฿)</x-label>
                                <x-input id="max_discount" name="max_discount" type="number" step="0.01" min="0" value="{{ old('max_discount', $coupon->max_discount ? (float)$coupon->max_discount : '') }}" />
                                @error('max_discount') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Usage Limit -->
                        <div class="space-y-2">
                            <x-label for="usage_limit">Total Usage Limit (Leave blank for unlimited)</x-label>
                            <x-input id="usage_limit" name="usage_limit" type="number" min="1" value="{{ old('usage_limit', $coupon->usage_limit) }}" />
                            @error('usage_limit') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <x-label for="starts_at">Start Date & Time</x-label>
                                <x-input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}" />
                                @error('starts_at') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <x-label for="expires_at">Expiration Date & Time</x-label>
                                <x-input id="expires_at" name="expires_at" type="datetime-local" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}" />
                                @error('expires_at') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Active Toggle -->
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="w-4 h-4 border-gray-300 rounded text-primary focus:ring-primary">
                            <span class="text-sm font-medium leading-none text-gray-700 dark:text-gray-300">Active</span>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end pt-4 space-x-4">
                            <x-button as="a" href="{{ route('admin.coupon.index') }}" variant="outline">Cancel</x-button>
                            <x-button type="submit">Update Coupon</x-button>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
