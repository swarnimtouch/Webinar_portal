@extends('layouts.admin')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card">
                    @if(session('success'))
                        <div class="alert alert-success m-6 mb-0">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger m-6 mb-0">{{ $errors->first() }}</div>
                    @endif
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            @if(auth()->user()->type === 'admin')
                                <select id="event-selector" class="form-select form-select-solid w-200px me-4">
                                    <option value="">Select Event</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}"
                                            {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                                            {{ $event->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif

                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                              transform="rotate(45 17.0365 15.1223)" fill="black"/>
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="black"/>
                                    </svg>
                                </span>
                                <input type="text" data-kt-user-table-filter="search"
                                       class="form-control form-control-solid w-250px ps-14"
                                       placeholder="Search fields"/>
                            </div>
                        </div>

                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end">
                                @if($selectedEventId)
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-field-modal">
                                        Add Dynamic Field
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <form id="fields-form" method="POST" action="{{ route('admin.dynamic_fields.save') }}">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $selectedEventId }}">
                            <input type="hidden" name="order_data" id="order-data">

                            <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_Dynamic_fields">
                                <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-80px">Move</th>
                                    <th class="min-w-125px">Index</th>
                                    <th class="min-w-150px">Field Name</th>
                                    <th class="min-w-150px">Label</th>
                                    <th class="min-w-150px">Icon</th>
                                    <th class="min-w-125px">Width</th>
                                    <th class="min-w-150px">Required</th>
                                    <th class="min-w-150px">Status</th>
                                    <th class="min-w-150px">Login With</th>
                                    <th class="min-w-80px">Action</th>
                                </tr>
                                </thead>

                                <tbody class="text-gray-600 fw-bold">
                                @forelse($fields as $field)
                                    <tr data-id="{{ $field->id }}">
                                        <td class="drag-handle" style="cursor:move;font-size:18px">☰</td>

                                        <td>
                                                <span class="badge bg-secondary index-badge">
                                                    {{ $loop->iteration }}
                                                </span>
                                        </td>
                                        <td>{{ $field->field_name }}</td>

                                        <td>
                                            <input type="text"
                                                   name="fields[{{ $field->id }}][label]"
                                                   value="{{ $field->label }}"
                                                   class="form-control form-control-sm">
                                        </td>

                                        <td>
                                            @php
                                                $defaultIcons = [
                                                    'first_name' => 'fa-solid fa-user', 'last_name' => 'fa-solid fa-user',
                                                    'email' => 'fa-solid fa-envelope', 'mobile_number' => 'fa-solid fa-phone',
                                                    'country' => 'fa-solid fa-earth-asia', 'state' => 'fa-solid fa-map',
                                                    'city' => 'fa-solid fa-building', 'password' => 'fa-solid fa-lock',
                                                ];
                                                $currentIcon = $field->icon ?: ($defaultIcons[$field->field_name] ?? 'fa-solid fa-pen');
                                            @endphp
                                            <select name="fields[{{ $field->id }}][icon]" class="form-select form-select-sm">
                                                @foreach([
                                                    'fa-solid fa-pen' => 'Pencil', 'fa-solid fa-user' => 'User',
                                                    'fa-solid fa-envelope' => 'Email', 'fa-solid fa-phone' => 'Phone',
                                                    'fa-solid fa-lock' => 'Lock', 'fa-solid fa-map' => 'Map',
                                                    'fa-solid fa-building' => 'Building', 'fa-solid fa-briefcase' => 'Work',
                                                    'fa-solid fa-location-dot' => 'Location', 'fa-solid fa-earth-asia' => 'Globe',
                                                    'fa-solid fa-calendar' => 'Calendar', 'fa-solid fa-list' => 'List',
                                                    'fa-solid fa-heart' => 'Heart', 'fa-solid fa-circle-question' => 'Question'
                                                ] as $icon => $iconLabel)
                                                    <option value="{{ $icon }}" @selected($currentIcon === $icon)>{{ $iconLabel }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <select name="fields[{{ $field->id }}][html_class]" class="form-select form-select-sm">
                                                <option value="half" @selected(($field->html_class ?: 'half') === 'half')>Half</option>
                                                <option value="full" @selected($field->html_class === 'full')>Full</option>
                                            </select>
                                        </td>

                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="hidden"
                                                       name="fields[{{ $field->id }}][is_required]"
                                                       value="0">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="fields[{{ $field->id }}][is_required]"
                                                       value="1"
                                                    {{ $field->is_required ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="hidden"
                                                       name="fields[{{ $field->id }}][status]"
                                                       value="inactive">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="fields[{{ $field->id }}][status]"
                                                       value="active"
                                                    {{ $field->status === 'active' ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            @if(in_array($field->field_name, ['email', 'mobile_number']))
                                                <div class="form-check">
                                                    <input class="form-check-input login-field-radio"
                                                           type="radio"
                                                           name="login_with"
                                                           value="{{ $field->id }}"
                                                        {{ $field->login_with == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        {{ $field->field_name == 'email' ? 'Email' : 'Mobile' }}
                                                    </label>
                                                </div>
                                            @elseif($field->field_name == 'password')
                                                <div class="form-check">
                                                    <input type="hidden" name="password_required" value="0">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="password-checkbox"
                                                           name="password_required"
                                                           value="1"
                                                        {{ $field->login_with == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="password-checkbox">
                                                        Password
                                                    </label>
                                                </div>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if($field->type === 'custom')
                                                <button type="button" class="btn btn-sm btn-light-danger delete-field" data-id="{{ $field->id }}">Delete</button>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            @if(auth()->user()->type === 'admin' && !$selectedEventId)
                                                Please select an event to view fields.
                                            @else
                                                No Dynamic Fields found
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6">
                        <button type="button" id="save-fields-btn" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($selectedEventId)
        <div class="modal fade" id="add-field-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('admin.dynamic_fields.store') }}">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $selectedEventId }}">
                    <div class="modal-header"><h2 class="modal-title">Add Dynamic Field</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-5"><label class="form-label required">Label</label><input name="label" value="{{ old('label') }}" class="form-control" required></div>
                        <div class="mb-5"><label class="form-label">Field name</label><input name="field_name" value="{{ old('field_name') }}" class="form-control" placeholder="Generated from label"><div class="form-text">Lowercase letters, numbers and underscores only.</div></div>
                        <div class="mb-5"><label class="form-label required">Input type</label><select name="attribute_id" id="new-field-type" class="form-select" required><option value="">Select input type</option>@foreach($attributes as $attribute)<option value="{{ $attribute->id }}" data-type="{{ $attribute->type }}" @selected(old('attribute_id') == $attribute->id)>{{ $attribute->name }}</option>@endforeach</select></div>
                        <div class="row mb-5">
                            <div class="col-6"><label class="form-label">Icon</label><select name="icon" class="form-select">@foreach(['fa-solid fa-pen'=>'Pencil','fa-solid fa-user'=>'User','fa-solid fa-envelope'=>'Email','fa-solid fa-phone'=>'Phone','fa-solid fa-lock'=>'Lock','fa-solid fa-building'=>'Building','fa-solid fa-briefcase'=>'Work','fa-solid fa-location-dot'=>'Location','fa-solid fa-map'=>'Map','fa-solid fa-earth-asia'=>'Globe','fa-solid fa-calendar'=>'Calendar','fa-solid fa-list'=>'List','fa-solid fa-heart'=>'Heart','fa-solid fa-circle-question'=>'Question'] as $icon => $iconLabel)<option value="{{ $icon }}" @selected(old('icon') === $icon)>{{ $iconLabel }}</option>@endforeach</select></div>
                            <div class="col-6"><label class="form-label required">Display width</label><select name="display_width" class="form-select" required><option value="half" @selected(old('display_width', 'half') === 'half')>Half width</option><option value="full" @selected(old('display_width') === 'full')>Full width</option></select></div>
                        </div>
                        <div class="mb-5 d-none" id="new-field-options"><label class="form-label required">Options</label><textarea name="options" class="form-control" rows="4" placeholder="One option per line">{{ old('options') }}</textarea></div>
                        <label class="form-check form-switch"><input type="checkbox" class="form-check-input" name="is_required" value="1" @checked(old('is_required'))><span class="form-check-label">Required</span></label>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Add Field</button></div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        "use strict";

        var KTDaynamicFieldList = function () {
            var table = document.getElementById('kt_table_Dynamic_fields');
            var datatable;

            var initUserTable = function () {
                if ($(table).find('tbody tr td[colspan]').length) return;

                datatable = $(table).DataTable({
                    searchDelay: 500,
                    processing: true,
                    ordering: false,
                    stateSave: false,
                    paging: false,
                    info: false,
                    columnDefs: [{orderable: false, targets: '_all'}]
                });
            }

            var handleSearchDatatable = function () {
                const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
                if (filterSearch) {
                    filterSearch.addEventListener('keyup', function (e) {
                        datatable.search(e.target.value).draw();
                    });
                }
            }

            return {
                init: function () {
                    if (!table) return;
                    initUserTable();
                    handleSearchDatatable();
                }
            }
        }();

        function initSortable() {
            function updateIndexes() {
                let order = [];
                $("#kt_table_Dynamic_fields tbody tr").each(function (index) {
                    $(this).find('.index-badge').text(index + 1);
                    const rowId = $(this).data('id');
                    if (rowId) {
                        order.push({id: rowId, index_no: index + 1});
                    }
                });
                $('#order-data').val(JSON.stringify(order));
            }

            if ($("#kt_table_Dynamic_fields tbody tr td[colspan]").length) return;

            $("#kt_table_Dynamic_fields tbody").sortable({
                handle: ".drag-handle",
                placeholder: "ui-state-highlight",
                helper: function (e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function (index) {
                        $(this).width($originals.eq(index).width());
                    });
                    return $helper;
                },
                update: function (event, ui) {
                    updateIndexes();
                }
            });

            updateIndexes();
        }

        function initSaveButton() {
            const saveBtn = document.getElementById('save-fields-btn');
            const form = document.getElementById('fields-form');

            if (saveBtn && form) {
                saveBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                    const formData = new FormData(form);

                    $.ajax({
                        url: form.action,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = 'Save';
                            toastr.success(response.message || "Changes saved successfully!");
                        },
                        error: function (xhr) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = 'Save';
                            Swal.fire({
                                text: xhr.responseJSON?.message || "Error saving changes. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {confirmButton: "btn fw-bold btn-primary"}
                            });
                        }
                    });
                });
            }
        }

        function initEventSelector() {
            const eventSelector = document.getElementById('event-selector');
            if (eventSelector) {
                eventSelector.addEventListener('change', function () {
                    const eventId = this.value;
                    if (eventId) {
                        window.location.href = '{{ route('admin.dynamic_fields') }}?event_id=' + eventId;
                    }
                });
            }
        }

        function initFieldActions() {
            const type = document.getElementById('new-field-type');
            const options = document.getElementById('new-field-options');
            const toggleOptions = () => options?.classList.toggle('d-none', !['select', 'radio', 'checkbox'].includes(type?.selectedOptions[0]?.dataset.type));
            type?.addEventListener('change', toggleOptions);
            toggleOptions();

            document.querySelectorAll('.delete-field').forEach(button => button.addEventListener('click', function () {
                Swal.fire({text: 'Delete this field and all saved answers?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete'}).then(result => {
                    if (!result.isConfirmed) return;
                    fetch('{{ route('admin.dynamic_fields.destroy', ':id') }}'.replace(':id', this.dataset.id) + '?event_id={{ $selectedEventId }}', {
                        method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                    }).then(response => { if (!response.ok) throw new Error(); location.reload(); }).catch(() => toastr.error('Unable to delete field.'));
                });
            }));
        }

        KTUtil.onDOMContentLoaded(function () {
            KTDaynamicFieldList.init();
            initSortable();
            initSaveButton();
            initEventSelector();
            initFieldActions();
        });
    </script>
@endpush
