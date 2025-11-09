<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $languages = Language::all();
        $categories = Category::all();

        return view('welcome', compact('languages', 'categories'));
    }

    public function store_category(Request $request)
    {
        Category::create([
            "name" => $request['name'],
        ]);
        return redirect()->back();
    }
}
