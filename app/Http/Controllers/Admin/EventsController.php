<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use App\Models\DynamicFields;
use App\Models\EventResource;
use App\Models\Events;
use App\Models\User;
use App\Support\EventStorage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventsController
{
    public function index()
    {
        return view('admin.events.index', ['title' => __('Events'), 'breadcrumb' => breadcrumb([__('Events') => route('admin.events')])]);
    }

    public function addEditForm($id = null)
    {
        $event = $id ? Events::with(['company', 'resources'])->findOrFail($id) : new Events();
        $selectedCompanyId = session()->getOldInput('company_id', $event->company_id);
        $selectedCompany = $selectedCompanyId ? Company::find($selectedCompanyId) : null;
        $eventSubAdmin = $event->exists
            ? User::where('type', 'sub_admin')->where('event_id', $event->id)->oldest()->first()
            : null;

        $response = [
            'event' => $event,
            'selectedCompany' => $selectedCompany,
            'eventResources' => $event->exists ? $event->resources->sortBy('slot')->values() : collect(),
            'eventSubAdmin' => $eventSubAdmin,
            'title' => __('Event'),
            'breadcrumb' => breadcrumb([__('Events') => route('admin.events'), ($id ? 'Edit' : 'Add' . ' Event') => '']),
        ];
        return view('admin.events.add_edit', $response);
    }


    public function save(Request $request, $id = null)
    {
        $event = $id ? Events::findOrFail($id) : new Events();
        $eventSubAdmin = $id
            ? User::where('type', 'sub_admin')->where('event_id', $id)->oldest()->first()
            : null;
        $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:255'],
            'admin_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->where(fn ($query) => $query->whereIn('type', ['admin', 'sub_admin']))
                    ->ignore($eventSubAdmin?->id),
                Rule::unique('users', 'email')
                    ->where(fn ($query) => $query->where('event_id', $id ?? 0))
                    ->ignore($eventSubAdmin?->id),
            ],
            'admin_password' => [
                Rule::requiredIf(fn () => !$eventSubAdmin),
                'nullable',
                'string',
                'min:8',
            ],
            'favicon' => [
                Rule::requiredIf(fn() => !$id),
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'logo' => [
                Rule::requiredIf(fn() => !$id),
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'player_id' => ['required', 'string'],
            'player_type' => ['required', 'string'],
            'player_iframe' => ['required', 'string'],
            'publish_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'session_agenda' => ['nullable', 'array'],
            'session_agenda.*.time' => ['nullable', 'string', 'max:50'],
            'session_agenda.*.duration' => ['nullable', 'string', 'max:50'],
            'session_agenda.*.title' => ['nullable', 'string', 'max:255'],
            'session_agenda.*.description' => ['nullable', 'string', 'max:1000'],
            'session_agenda.*.status' => ['nullable', 'in:upcoming,live,completed'],
            'resource_id' => ['nullable', 'array'],
            'resource_id.*' => ['nullable', 'integer'],
            'resource_title' => ['nullable', 'array'],
            'resource_title.*' => ['nullable', 'string', 'max:255'],
            'resource_file' => ['nullable', 'array'],
            'resource_file.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $company = Company::findOrFail($request->integer('company_id'));

        $event->slug = generate_slug($request->name, 'events', $event->id);

        $event->company_id = $company->id;
        $event->domain = $company->slug;
        $event->name = $request->name ?? null;
        $event->email = $request->email ?? null;
        $event->phone = $request->phone ?? null;
        $event->description = $request->description ?? null;
        $event->session_agenda = collect($request->input('session_agenda', []))
            ->filter(fn($item) => filled($item['title'] ?? null))
            ->map(fn($item) => [
                'time' => $item['time'] ?? '',
                'duration' => $item['duration'] ?? '',
                'title' => $item['title'],
                'description' => $item['description'] ?? '',
                'status' => $item['status'] ?? 'upcoming',
            ])->values()->all();

        $event->save();

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            EventStorage::delete($event->getRawOriginal('favicon'), 'events/' . $event->getRawOriginal('favicon'));
            $event->favicon = EventStorage::store($file, $event, 'event-assets', $name);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            EventStorage::delete($event->getRawOriginal('logo'), 'events/' . $event->getRawOriginal('logo'));
            $event->logo = EventStorage::store($file, $event, 'event-assets', $name);
        }

        $event->footer_text = $request->footer_text ?? null;
        $event->player_id = $request->player_id ?? null;
        $event->player_type = $request->player_type ?? null;
        $event->player_iframe = $request->player_iframe ?? null;
        $event->publish_date = $request->publish_date ? Carbon::parse($request->publish_date)->format('Y-m-d') : null;
        $event->start_time = $request->start_time ? Carbon::parse($request->start_time)->format('Y-m-d H:i:s') : null;
        $event->end_time = $request->end_time ? Carbon::parse($request->end_time)->format('Y-m-d H:i:s') : null;
        $event->active_user_from = $request->active_user_from ? Carbon::parse($request->active_user_from)->format('Y-m-d H:i:s') : null;
        $event->active_user_to = $request->active_user_to ? Carbon::parse($request->active_user_to)->format('Y-m-d H:i:s') : null;
        $event->is_log_attendance = $request->is_log_attendance ?? 0;
        $event->save();

        $subAdmin = $eventSubAdmin ?: new User();
        $subAdmin->type = 'sub_admin';
        $subAdmin->event_id = $event->id;
        $subAdmin->email = $request->admin_email;
        $subAdmin->name = $subAdmin->name ?: Str::before($request->admin_email, '@');
        $subAdmin->status = 'active';
        if ($request->filled('admin_password')) {
            $subAdmin->password = Hash::make($request->admin_password);
        }
        $subAdmin->save();

        $this->saveResources($request, $event);
        $isDynamicFieldsExist = DynamicFields::where('event_id', $event->id)->count();
        if ($isDynamicFieldsExist == 0) {
            foreach (get_dynamic_fields() as $key => $fields) {
                $fields['event_id'] = $event->id;
                $fields['is_required'] = 1;
                $fields['created_at'] = now();
                $fields['updated_at'] = now();
                DynamicFields::insert($fields);
            }
        }
        return redirect()->route('admin.events')
            ->with('success', 'Event Saved Successfully');
    }


    public function delete($id)
    {
        try {
            $event = Events::findOrFail($id);
            EventStorage::delete($event->getRawOriginal('favicon'), 'events/' . $event->getRawOriginal('favicon'));
            EventStorage::delete($event->getRawOriginal('logo'), 'events/' . $event->getRawOriginal('logo'));
            $this->deleteResourceFiles($event);
            $event->delete();

            return response()->json(['success' => true, 'message' => 'Event deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting event'], 500);
        }
    }


    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No events selected'], 400);
            }
            $events = Events::whereIn('id', $ids)->get();
            foreach ($events as $event) {
                EventStorage::delete($event->getRawOriginal('favicon'), 'events/' . $event->getRawOriginal('favicon'));
                EventStorage::delete($event->getRawOriginal('logo'), 'events/' . $event->getRawOriginal('logo'));
                $this->deleteResourceFiles($event);
                $event->delete();
            }
            return response()->json(['success' => true, 'message' => 'Events deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting events'], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $event = Events::findOrFail($id);
            $event->status = $request->input('status');
            $event->save();
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $event->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status'
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $query = Events::with('company');
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('company', fn($company) => $company->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->has('status') && !empty($request->status)) {
            $status = $request->status;
            $query->where('status', $status);
        }
        $total = $query->count();
        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];
                $dbColumn = match ($columnName) {
                    'name' => 'name',
                    'slug' => 'slug',
                    'domain' => 'domain',
                    'company' => 'domain',
                    'email' => 'email',
                    'phone' => 'phone',
                    default => 'id'
                };
                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }
        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $events = $query->skip($start)->take($length)->get();
        $data = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'logo' => $event->logo,
                'name' => $event->name,
                'slug' => $event->slug,
                'domain' => $event->domain,
                'company' => $event->company?->name ?? Str::headline($event->domain),
                'public_url' => $event->public_url,
                'email' => $event->email,
                'phone' => $event->phone,
                'status' => $event->status,
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

    public function searchCompanies(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $companies = Company::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($companyQuery) use ($search) {
                    $companyQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'slug']);

        return response()->json(['companies' => $companies]);
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $name = trim($validated['name']);
        $existing = Company::where('name', $name)->first();
        if ($existing) {
            return response()->json(['company' => $existing]);
        }

        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 2;
        while (Company::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        $company = Company::create([
            'name' => $name,
            'slug' => $slug,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return response()->json(['company' => $company], 201);
    }

    private function saveResources(Request $request, Events $event): void
    {
        $submittedIds = collect($request->input('resource_id', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values();

        $event->resources()
            ->whereNotIn('id', $submittedIds)
            ->get()
            ->each(function (EventResource $resource) {
                EventStorage::delete($resource->file_path);
                $resource->delete();
            });

        $titles = $request->input('resource_title', []);
        $ids = $request->input('resource_id', []);
        $files = $request->file('resource_file', []);
        $indexes = collect(array_keys($titles))
            ->merge(array_keys($ids))
            ->merge(array_keys($files))
            ->unique();
        $nextSlot = (int) $event->resources()->max('slot') + 1;

        foreach ($indexes as $index) {
            $resourceId = $ids[$index] ?? null;
            $resource = $resourceId
                ? $event->resources()->whereKey($resourceId)->first()
                : null;
            $title = trim((string) ($titles[$index] ?? ''));
            $file = $files[$index] ?? null;

            if (!$resource && !$file) {
                continue;
            }

            if ($file) {
                $slot = $resource?->slot ?? $nextSlot++;
                $storedPath = EventStorage::store($file, $event, 'resource', "resource-{$slot}-" . Str::uuid() . '.pdf');

                if ($resource) {
                    EventStorage::delete($resource->file_path);
                }

                ($resource ?: new EventResource([
                    'event_id' => $event->id,
                    'slot' => $slot,
                ]))->fill([
                    'title' => $title ?: "Resource {$slot}",
                    'file_path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                ])->save();
            } elseif ($resource) {
                $resource->update(['title' => $title ?: "Resource {$resource->slot}"]);
            }
        }
    }

    private function deleteResourceFiles(Events $event): void
    {
        $event->resources()->each(function (EventResource $resource) {
            EventStorage::delete($resource->file_path);
        });
    }
}
