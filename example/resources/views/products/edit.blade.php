@extends('layout')

@section('title', 'Edit Product')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Edit Product: {{ $product->sku }}</h2>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 mb-6">
            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                <input type="text" name="sku" id="sku" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    value="{{ old('sku', $product->sku) }}">
                @error('sku')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        value="{{ old('price', $product->price) }}">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                    <input type="number" name="stock" id="stock" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        value="{{ old('stock', $product->stock) }}">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="border-t pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Translations</h3>

            @foreach($languages as $language)
                @php
                    $translation = $product->getTranslationsByLanguage($language->id);
                @endphp
                <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">
                        {{ $language->title }} ({{ $language->code }})
                        @if($translation)
                            <span class="ml-2 text-xs text-green-600">✓ Translated</span>
                        @endif
                    </h4>

                    <div class="space-y-4">
                        <div>
                            <label for="name_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="translations[{{ $language->id }}][name]" id="name_{{ $language->id }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ $translation->name ?? '' }}">
                        </div>

                        <div>
                            <label for="short_description_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                            <textarea name="translations[{{ $language->id }}][short_description]" id="short_description_{{ $language->id }}" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $translation->short_description ?? '' }}</textarea>
                        </div>

                        <div>
                            <label for="description_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="translations[{{ $language->id }}][description]" id="description_{{ $language->id }}" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $translation->description ?? '' }}</textarea>
                        </div>

                        @if($translation && $translation->slug)
                            <div class="text-xs text-gray-500">
                                Slug: <span class="font-mono">{{ $translation->slug }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
