<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Menu Management') }}
            </h2>
            <x-button as="a" href="{{ route('admin.menu.create') }}">
                Add Menu Item
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
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Image</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Category</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                @forelse ($menuItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->image_url)
                                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="object-cover w-10 h-10 rounded-full">
                                            @else
                                                <div class="w-10 h-10 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->price, 2) }} ฿</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-badge variant="{{ $item->is_available ? 'default' : 'secondary' }}">
                                                {{ $item->is_available ? 'Available' : 'Out of Stock' }}
                                            </x-badge>
                                        </td>
                                        <td class="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
                                            <x-link href="{{ route('admin.menu.edit', $item) }}">Edit</x-link>
                                            <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this specific category it may break things?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="destructive" size="sm" type="submit">Delete</x-button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No menu items found. Get started by adding one!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $menuItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
