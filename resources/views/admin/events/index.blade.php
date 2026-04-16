@extends('layouts.admin')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
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
                                       placeholder="Search Events"/>
                            </div>
                        </div>

                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                {{-- Filter --}}
                                <button type="button" class="btn btn-light-primary me-3"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                fill="black"/>
                                        </svg>
                                    </span>
                                    Filter
                                </button>
                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                    </div>
                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5" data-kt-user-table-filter="form">
                                        <div class="mb-10">
                                            <label class="form-label fs-6 fw-bold">Status:</label>
                                            <select class="form-select form-select-solid fw-bolder"
                                                    data-kt-select2="true"
                                                    data-placeholder="Select option"
                                                    data-allow-clear="true"
                                                    data-kt-user-table-filter="status"
                                                    data-hide-search="true">
                                                <option></option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="reset"
                                                    class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-user-table-filter="reset">Reset
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-primary fw-bold px-6"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-user-table-filter="filter">Apply
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('admin.events.add_edit_form') }}" class="btn btn-primary">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1"
                                                  transform="rotate(-90 11.364 20.364)" fill="black"/>
                                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black"/>
                                        </svg>
                                    </span>
                                    Add Event
                                </a>
                            </div>

                            <div class="d-flex justify-content-end align-items-center d-none"
                                 data-kt-user-table-toolbar="selected">
                                <div class="fw-bolder me-5">
                                    <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                                </div>
                                <button type="button" class="btn btn-danger"
                                        data-kt-user-table-select="delete_selected">
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_events">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th></th>
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_events .row-checkbox"
                                               value="1"/>
                                    </div>
                                </th>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Domain</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        "use strict";

        const qs = (s, p = document) => p.querySelector(s);
        const qsa = (s, p = document) => [...p.querySelectorAll(s)];
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

        let eventTable;

        function initEventTable() {
            eventTable = $('#kt_table_events').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 500,
                ajax: {
                    url: '{{ route("admin.events.datatable") }}',
                    data: d => {
                        d.search = qs('[data-kt-user-table-filter="search"]').value;
                        d.status = qs('[data-kt-user-table-filter="status"]')?.value ?? '';
                    }
                },
                order: [[1, 'desc']],
                pageLength: 10,
                columns: [
                    {data: null, orderable: false, defaultContent: ''},
                    {
                        data: 'id', orderable: false, searchable: false,
                        render: id =>
                            `<div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input row-checkbox" type="checkbox" value="${id}">
                    </div>`
                    },
                    {
                        data: 'logo', orderable: false,
                        render: (data, type, row) =>
                            data
                                ? `<img src="${data}" width="50" height="50"
                               style="object-fit:contain;border-radius:4px;background:#f5f8fa;"
                               alt="Logo">`
                                : '<span class="text-muted">—</span>'
                    },

                    {data: 'name'},
                    {data: 'domain'},
                    {data: 'email'},
                    {data: 'phone'},

                    // Status toggle
                    {
                        data: 'status', orderable: false,
                        render: (data, type, row) =>
                            `<div class="form-check form-switch">
                        <input class="form-check-input event-status-toggle"
                               type="checkbox"
                               data-id="${row.id}"
                               ${row.status === 'active' ? 'checked' : ''}>
                    </div>`
                    },

                    // Actions dropdown
                    {
                        data: 'id', orderable: false, searchable: false,
                        render: id =>
                            `<div class="dropdown">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Actions
                            <span class="svg-icon svg-icon-5 m-0">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584
                                             5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929
                                             15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25
                                             10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358
                                             8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533
                                             13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                          fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end py-4">
                            <a href="{{ route('admin.events.add_edit_form') }}/${id}"
                               class="dropdown-item px-3">Edit</a>
                            <a href="#" class="dropdown-item px-3 text-danger event-delete"
                               data-id="${id}">Delete</a>
                        </div>
                    </div>`
                    }
                ]
            });
        }

        // ─── FIX: Delegated click listener for dynamically rendered delete links ────
        document.addEventListener('click', function (e) {
            const link = e.target.closest('.event-delete');
            if (!link) return;

            e.preventDefault();
            e.stopPropagation(); // prevent dropdown re-opening

            const id = link.dataset.id;
            confirmDelete(id);
        });

        // ─── Single delete ────────────────────────────────────────────────────────────
        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete Event?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger fw-bold',
                    cancelButton: 'btn btn-light fw-bold'
                },
                buttonsStyling: false
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route("admin.events.delete", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function () {
                        eventTable.draw(false);
                        toastr.success('Event deleted successfully!');
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not delete the event. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        }

        // ─── Status toggle ────────────────────────────────────────────────────────────
        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('event-status-toggle')) return;

            const checkbox = e.target;
            const id = checkbox.dataset.id;
            const status = checkbox.checked ? 'active' : 'inactive';

            Swal.fire({
                text: `Set event status to "${status}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                customClass: {
                    confirmButton: 'btn btn-primary fw-bold',
                    cancelButton: 'btn btn-light fw-bold'
                },
                buttonsStyling: false
            }).then(result => {
                if (!result.isConfirmed) {
                    checkbox.checked = !checkbox.checked; // revert
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.events.toggleStatus", ":id") }}'.replace(':id', id),
                    method: 'POST',
                    data: {_token: '{{ csrf_token() }}', status},
                    success: function () {
                        toastr.success('Status updated successfully!');
                    },
                    error: function () {
                        checkbox.checked = !checkbox.checked; // revert on error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not update status. Please try again.'
                        });
                    }
                });
            });
        });

        // ─── Select-all checkbox ──────────────────────────────────────────────────────
        document.addEventListener('change', function (e) {
            if (!e.target.matches('[data-kt-check="true"]')) return;
            qsa('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
            updateToolbar();
        });

        // ─── Per-row checkbox ─────────────────────────────────────────────────────────
        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('row-checkbox')) return;
            updateToolbar();
        });

        function updateToolbar() {
            const selectedCount = qsa('.row-checkbox:checked').length;
            const toolbarBase = qs('[data-kt-user-table-toolbar="base"]');
            const toolbarSelected = qs('[data-kt-user-table-toolbar="selected"]');
            const countEl = qs('[data-kt-user-table-select="selected_count"]');

            if (selectedCount > 0) {
                toolbarBase.classList.add('d-none');
                toolbarSelected.classList.remove('d-none');
                countEl.textContent = selectedCount;
            } else {
                toolbarBase.classList.remove('d-none');
                toolbarSelected.classList.add('d-none');
                countEl.textContent = '';
            }

            // Disable individual status toggles when multiple rows selected
            qsa('.event-status-toggle').forEach(t => t.disabled = selectedCount > 1);
        }

        // ─── Bulk delete ──────────────────────────────────────────────────────────────
        qs('[data-kt-user-table-select="delete_selected"]')
            ?.addEventListener('click', () => {
                const ids = qsa('.row-checkbox:checked').map(cb => cb.value);

                if (!ids.length) {
                    Swal.fire({icon: 'info', text: 'Please select at least one event.', confirmButtonText: 'OK'});
                    return;
                }

                Swal.fire({
                    title: `Delete ${ids.length} event(s)?`,
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete all',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger fw-bold',
                        cancelButton: 'btn btn-light fw-bold'
                    },
                    buttonsStyling: false
                }).then(result => {
                    if (!result.isConfirmed) return;

                    fetch('{{ route("admin.events.deleteMultiple") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ids})
                    })
                        .then(res => {
                            if (!res.ok) throw new Error();
                            toastr.success('Selected events deleted successfully.');
                            eventTable.draw(false);
                        })
                        .catch(() => {
                            Swal.fire({icon: 'error', text: 'Failed to delete events. Please try again.'});
                        });
                });
            });

        // ─── Search & filter wiring ───────────────────────────────────────────────────
        qs('[data-kt-user-table-filter="search"]')
            .addEventListener('keyup', () => eventTable.draw());

        qs('[data-kt-user-table-filter="filter"]')
            ?.addEventListener('click', () => eventTable.draw());

        qs('[data-kt-user-table-filter="reset"]')
            ?.addEventListener('click', () => {
                const statusEl = qs('[data-kt-user-table-filter="status"]');
                if (statusEl) statusEl.value = '';
                eventTable.draw();
            });

        // ─── Init ─────────────────────────────────────────────────────────────────────
        KTUtil.onDOMContentLoaded(() => initEventTable());
    </script>
@endpush
