<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'translationRelation'])->latest()->get();
        $languages = Language::all();

        return view('posts.index', compact('posts', 'languages'));
    }

    public function create()
    {
        $languages = Language::all();

        return view('posts.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['user_id'] = 1;

        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $languages = Language::all();
        $post->load('translationRelation');

        return view('posts.edit', compact('post', 'languages'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }

    public function show(Post $post, $locale = 'en')
    {
        $translation = $post->getTranslationsByLanguage($locale, 'code');

        if (!$translation) {
            abort(404, 'Post translation not found');
        }

        return view('posts.show', compact('post', 'translation', 'locale'));
    }
}
