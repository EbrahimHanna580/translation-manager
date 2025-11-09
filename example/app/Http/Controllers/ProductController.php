<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('translationRelation')->latest()->get();
        $languages = Language::all();

        return view('products.index', compact('products', 'languages'));
    }

    public function create()
    {
        $languages = Language::all();

        return view('products.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $languages = Language::all();
        $product->load('translationRelation');

        return view('products.edit', compact('product', 'languages'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    public function show(Product $product, $locale = 'en')
    {
        $translation = $product->getTranslationsByLanguage($locale, 'code');

        if (!$translation) {
            abort(404, 'Product translation not found');
        }

        return view('products.show', compact('product', 'translation', 'locale'));
    }
}
