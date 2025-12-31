<?php

namespace App\Http\Controllers\Admin;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pagination
        $contents = Content::orderBy('created_at', 'desc')->paginate(20);

        $title = 'Content';
        $breadcrumbs = [
            'Content' => ''
        ];

        return view('admin.content.index', compact(
            'contents',
            'title',
            'breadcrumbs'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $content = Content::where('slug', $slug)->firstOrFail();

        return view('admin.content.show', compact('content'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $content = Content::findOrFail($id);

        // 🔹 Edit page pe bhi same breadcrumb & title
        $title = 'Content';
        $breadcrumbs = [
            'Content' => route('content'),
            'Edit Content' => ''
        ];

        return view('admin.content.edit', compact(
            'content',
            'title',
            'breadcrumbs'
        ));
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'slug'    => 'required|string|max:255|unique:contents,slug,' . $id,
            'content' => 'required|string',
        ]);

        $content = Content::findOrFail($id);

        $content->update($validated);

        return redirect()
            ->route('content')   // 🔴 make sure route exists
            ->with('success', 'Content updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        //
    }
}
