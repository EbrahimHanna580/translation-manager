@extends('layout')

@section('title', 'Edit Post')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Edit Post #{{ $post->id }}</h2>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Published</span>
                    </label>
                </div>

                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published At</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
            </div>
        </div>

        <div class="border-t pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Translations</h3>

            @foreach($languages as $language)
                @php
                    $translation = $post->getTranslationsByLanguage($language->id);
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
                            <label for="title_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="translations[{{ $language->id }}][title]" id="title_{{ $language->id }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                value="{{ $translation->title ?? '' }}">
                        </div>

                        <div>
                            <label for="content_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                            <textarea name="translations[{{ $language->id }}][content]" id="content_{{ $language->id }}" rows="8"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $translation->content ?? '' }}</textarea>
                        </div>

                        <div>
                            <label for="meta_description_{{ $language->id }}" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea name="translations[{{ $language->id }}][meta_description]" id="meta_description_{{ $language->id }}" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $translation->meta_description ?? '' }}</textarea>
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
            <a href="{{ route('posts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                Update Post
            </button>
        </div>
    </form>
</div>
@endsection
