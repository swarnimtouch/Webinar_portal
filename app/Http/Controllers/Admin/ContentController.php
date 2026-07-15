<?php

namespace App\Http\Controllers\Admin;

use App\Models\Content;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pagination
        return view('admin.content.index', [
            'title' => __('Contents'),
            'breadcrumb' => breadcrumb([
                __('Contents') => route('admin.content')
            ])
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($contentSlug)
    {
        $content = Content::where('slug', $contentSlug)->firstOrFail();

        return view('admin.content.show', compact('content'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function addEditForm($id = null)
    {
        $admin = auth()->user();
        $content = $id ? Content::query()
            ->when($admin->type === 'sub_admin', fn($q) => $q->where('event_id', $admin->event_id))
            ->findOrFail($id) : new Content();

        $response = [
            'content' => $content,
            'events' => $admin->type === 'admin' ? Events::orderBy('name')->get() : collect(),
            'title' => __('Content'),
            'breadcrumb' => breadcrumb([__('Contents') => route('admin.content'), ($id ? 'Edit' : 'Add' . ' Content') => '']),
        ];
        return view('admin.content.edit', $response);


    }


    public function save(Request $request, $id = null)
    {
        $admin = auth()->user();
        $eventId = $admin->type === 'sub_admin' ? $admin->event_id : $request->integer('event_id');

        $validated = $request->validate([
            'event_id' => $admin->type === 'admin' ? ['required', 'exists:events,id'] : ['nullable'],
            'title' => 'required|string|max:255',
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('contents', 'slug')->where(fn($q) => $q->where('event_id', $eventId))->ignore($id),
            ],
            'content' => 'required|string',
        ]);

        $content = $id ? Content::query()
            ->when($admin->type === 'sub_admin', fn($q) => $q->where('event_id', $admin->event_id))
            ->findOrFail($id) : new Content();

        $validated['event_id'] = $eventId;
        $content->fill($validated)->save();

        return redirect()
            ->route('admin.content')
            ->with('success', 'Content Save successfully!');
    }

    public function datatable(Request $request)
    {
        $admin = auth()->user();
        $query = Content::with('event')
            ->when($admin->type === 'sub_admin', fn($q) => $q->where('event_id', $admin->event_id));

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('event', fn($eventQuery) => $eventQuery->where('name', 'like', "%{$search}%"));
            });
        }


        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];

                $dbColumn = match ($columnName) {
                    'title' => 'title',
                    'slug' => 'slug',
                    'created_at' => 'created_at',
                    default => 'id'
                };

                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $contents = $query->skip($start)->take($length)->get();

        $data = $contents->map(function ($content) {
            return [
                'id' => $content->id,
                'event_name' => $content->event?->name ?? '-',
                'title' => $content->title,
                'slug' => $content->slug,
                'content' => $content->content,
                'created_at' => $content->created_at->format('d M Y'),
                'actions' => '',
            ];
        });

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }
}
