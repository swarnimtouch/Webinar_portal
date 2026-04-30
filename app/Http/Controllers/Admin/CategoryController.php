<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

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
            'slug' => strtolower(str_replace(' ','-',$request->name)),
        ]);

        return redirect()->route('category.index')->with('success','Category Added');
    }

    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        $category->update([
            'name' => $request->name,
            'slug' => strtolower(str_replace(' ','-',$request->name)),
        ]);

        return redirect()->route('category.index')->with('success','Updated');
    }

    public function destroy($id)
    {
        Category::find($id)->delete();
        return back()->with('success','Deleted');
    }
}
