@extends('layout')

@section('title', 'Create Product')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Create New Product</h2>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-6 mb-6">
            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                <input type="text" name="sku" id="sku" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    value="{{ old('sku') }}">
                @error('sku')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        value="{{ old('price') }}">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                    <input type="number" name="stock" id="stock" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        value="{{ old('stock', 0) }}">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="border-t pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Translations</h3>

            @foreach($languages as $language)
                <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">{{ $language->title }} ({{ $language->code }})</h4>

                    <div class="space-y-4">
                        <div>
                            <label for="name_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="translations[{{ $language->id }}][name]" id="name_{{ $language->id }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="short_description_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                            <textarea name="translations[{{ $language->id }}][short_description]" id="short_description_{{ $language->id }}" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label for="description_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="translations[{{ $language->id }}][description]" id="description_{{ $language->id }}" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                Create Product
            </button>
        </div>
    </form>
</div>
@endsection
