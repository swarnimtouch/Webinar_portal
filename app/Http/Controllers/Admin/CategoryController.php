<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
     public function index()
    {
        return "Category Page Working";
    }
    // public function index()
    // {
    //     $categories = Category::latest()->get();
    //     return view('admin.category.index', compact('categories'));
    // }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
        ]);

        return redirect()->route('admin.category.index')->with('success','Category Added');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
        ]);

        return redirect()->route('admin.category.index')->with('success','Updated');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('success','Deleted');
    }
}
