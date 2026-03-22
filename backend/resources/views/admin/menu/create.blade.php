<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Add New Menu Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-card>
                <div class="p-6">
                    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Name -->
                        <div class="space-y-2">
                            <x-label for="name">Name</x-label>
                            <x-input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus />
                            @error('name') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <x-label for="category_id">Category</x-label>
                            <select id="category_id" name="category_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <x-label for="description">Description</x-label>
                            <x-textarea id="description" name="description" rows="3">{{ old('description') }}</x-textarea>
                            @error('description') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <!-- Price & Prep Time -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <x-label for="price">Price (฿)</x-label>
                                <x-input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" required />
                                @error('price') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <x-label for="prep_time_minutes">Prep Time (Minutes)</x-label>
                                <x-input id="prep_time_minutes" name="prep_time_minutes" type="number" min="0" value="{{ old('prep_time_minutes', 0) }}" />
                                @error('prep_time_minutes') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="space-y-2">
                            <x-label for="image">Menu Image</x-label>
                            <input id="image" name="image" type="file" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/90" />
                            @error('image') <p class="mt-1 text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <!-- Toggles -->
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }} class="text-primary rounded border-gray-300 focus:ring-primary h-4 w-4">
                                <span class="text-sm font-medium leading-none text-gray-700 dark:text-gray-300">Available</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="is_popular" value="1" {{ old('is_popular', false) ? 'checked' : '' }} class="text-primary rounded border-gray-300 focus:ring-primary h-4 w-4">
                                <span class="text-sm font-medium leading-none text-gray-700 dark:text-gray-300">Popular</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end pt-4 space-x-4">
                            <x-button as="a" href="{{ route('admin.menu.index') }}" variant="outline">Cancel</x-button>
                            <x-button type="submit">Save Menu Item</x-button>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
