@extends('layouts.admin')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
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
                                       placeholder="Search user"/>
                            </div>
                        </div>

                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Filter-->
                                @if(auth()->user()->type === 'admin')

                                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                        <!--begin::Filter-->
                                        <button type="button" class="btn btn-light-primary me-3"
                                                data-kt-menu-trigger="click"
                                                data-kt-menu-placement="bottom-end">
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
                                        <!--begin::Menu 1-->

                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                             data-kt-menu="true">
                                            <!--begin::Header-->
                                            <div class="px-7 py-5">
                                                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                            </div>
                                            <!--end::Header-->
                                            <div class="separator border-gray-200"></div>
                                            <!--begin::Content-->

                                            <div class="px-7 py-5" data-kt-user-table-filter="form">
                                                <!--begin::Input group-->
                                                <div class="mb-10">
                                                    <label class="form-label fs-6 fw-bold">Event:</label>
                                                    <select class="form-select form-select-solid fw-bolder"
                                                            data-kt-select2="true"
                                                            data-placeholder="Select option" data-allow-clear="true"
                                                            data-kt-user-table-filter="event" data-hide-search="true">
                                                        <option value="">-- Select Event --</option>
                                                        @foreach($events ?? [] as $event)
                                                            <option
                                                                value="{{ $event->id }}">{{ $event->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>


                                                <!--end::Input group-->
                                                <!--begin::Actions-->
                                                <div class="d-flex justify-content-end">
                                                    <button type="reset"
                                                            class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                            data-kt-menu-dismiss="true"
                                                            data-kt-user-table-filter="reset">
                                                        Reset
                                                    </button>
                                                    <button type="submit" class="btn btn-primary fw-bold px-6"
                                                            data-kt-menu-dismiss="true"
                                                            data-kt-user-table-filter="filter">
                                                        Apply
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                            </div>
                                            <!--end::Content-->
                                        </div>

                                        <!--end::Menu 1-->
                                        <!--end::Filter-->
                                        <!--begin::Add poll-->

                                        <!--end::Add poll-->
                                    </div>
                                @endif
                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                     id="filter-menu">

                                    <div id="dynamic-filters"></div>

                                </div>
                                <!--end::Menu-->
                                <!--end::Filter-->

                                <!--begin::Export-->
                                <button type="button" class="btn btn-light-primary me-3" id="export-btn">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1"
                                                  transform="rotate(90 12.75 4.25)" fill="black"/>
                                            <path
                                                d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                                fill="black"/>
                                            <path
                                                d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                                fill="#C4C4C4"/>
                                        </svg>
                                    </span>
                                    Export
                                </button>
                                <!--end::Export-->


                            </div>
                            <!--end::Toolbar-->

                            <!--begin::Group actions-->
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
                            <!--end::Group actions-->
                        </div>

                    </div>

                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_user_attendance">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_user_attendance .form-check-input"
                                               value="1"/>
                                    </div>
                                </th>
                                @if(auth()->user()->type === 'admin')
                                    <th>Event</th>
                                @endif
                                @foreach($active_fields as $field)
                                    <th class="min-w-150px">{{ $field->label }}</th>
                                @endforeach
                                <th class="min-w-150px">Session Time</th>
                                <th class="min-w-150px">Registration Date</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
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

        var KTUserAttendanceList = function () {
            var table = document.getElementById('kt_table_user_attendance');
            const isAdmin = {{auth()->user()->type === 'admin' ? 'true' : 'false'}};
            var datatable;
            var toolbarBase;
            var toolbarSelected;
            var selectedCount;

            var initUserTable = function () {
                const active_fields = @json($active_fields->pluck('field_name'));

                const columns = [

                    {
                        data: 'attendance_id',
                        orderable: false,
                        searchable: false,
                        render: id => `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input row-checkbox" type="checkbox" value="${id}" />
                            </div>`
                    },
                    ...(isAdmin ? [{data: 'event'}] : []),

                ];


                active_fields.forEach(fieldName => {
                    columns.push({
                        data: fieldName,
                        render: data => data || '-'
                    });
                });

                columns.push(
                    {
                        data: 'session_time',
                        render: function (seconds) {
                            if (!seconds || seconds <= 0) return '0h 0m 0s';

                            const h = Math.floor(seconds / 3600);
                            const m = Math.floor((seconds % 3600) / 60);
                            const s = seconds % 60;

                            return `${h}h ${m}m ${s}s`;
                        }
                    },
                    {
                        data: 'registration_date',
                        render: date => date || '-'
                    },

                    {
                        data: 'attendance_id',
                        orderable: false,
                        searchable: false,
                        render: id => `
                            <div class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-bs-toggle="dropdown">
                                    Actions
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4">
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 attendance-delete" data-id="${id}">Delete</a>
                                    </div>
                                </div>
                            </div>`
                    }
                );

                datatable = $(table).DataTable({
                    processing: true,
                    serverSide: true,
                    searchDelay: 500,
                    ajax: {
                        url: '{{ route("admin.user_attendance.datatable") }}',
                        data: d => {
                            d.search = document.querySelector('[data-kt-user-table-filter="search"]').value;
                            if (isAdmin) {
                                const eventEl = document.querySelector('[data-kt-user-table-filter="event"]');
                                if (eventEl) d.event = eventEl.value;
                            }

                        }
                    },
                    order: [[active_fields.length + 2, 'desc']],
                    pageLength: 10,
                    columns: columns
                });

                datatable.on('draw', function () {
                    initToggleToolbar();
                    toggleToolbars();
                    KTMenu.createInstances();
                });
            };


            document.addEventListener('click', e => {
                const del = e.target.closest('.attendance-delete');
                if (del) {
                    e.preventDefault();
                    confirmDelete(del.dataset.id);
                }
            });

            function confirmDelete(id) {
                Swal.fire({
                    text: "Delete this attendance record?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Delete"
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '{{ route("admin.user_attendance.delete", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        data: {_token: '{{ csrf_token() }}'},
                        success: function (data) {
                            toastr.success(data.message);
                            datatable.draw(false);
                        },
                        error: function () {
                            toastr.error('Failed to delete attendance record.');
                        }
                    });
                });
            }

            var handleSearchDatatable = function () {
                const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
                filterSearch.addEventListener('keyup', function (e) {
                    datatable.search(e.target.value).draw();
                });
            }

            var toggleToolbars = function () {
                toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
                toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
                selectedCount = document.querySelector('[data-kt-user-table-select="selected_count"]');

                const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');
                let count = [...allCheckboxes].filter(c => c.checked).length;

                if (count > 0) {
                    selectedCount.innerHTML = count;
                    if (toolbarBase) toolbarBase.classList.add('d-none');
                    if (toolbarSelected) toolbarSelected.classList.remove('d-none');
                } else {
                    if (toolbarBase) toolbarBase.classList.remove('d-none');
                    if (toolbarSelected) toolbarSelected.classList.add('d-none');
                }
            };

            var initToggleToolbar = function () {
                const checkboxes = table.querySelectorAll('tbody [type="checkbox"]');
                const headerCheckbox = table.querySelector('thead [type="checkbox"]');

                if (headerCheckbox) {
                    headerCheckbox.addEventListener('change', function (e) {
                        checkboxes.forEach(c => c.checked = e.target.checked);
                        toggleToolbars();
                    });
                }

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', toggleToolbars);
                });
            };

            // Export
            function handleExport() {
                const exportBtn = document.getElementById('export-btn');
                if (!exportBtn) return;

                exportBtn.addEventListener('click', function () {
                    const originalHTML = exportBtn.innerHTML;
                    exportBtn.disabled = true;
                    exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';

                    const url = new URL('{{ route("admin.user_attendance.export") }}', window.location.origin);
                    const searchValue = document.querySelector('[data-kt-user-table-filter="search"]')?.value ?? '';

                    if (searchValue) url.searchParams.set('search', searchValue);

                    fetch(url.toString(), {
                        method: 'GET',
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    })
                        .then(res => {
                            if (!res.ok) throw new Error();
                            return res.blob();
                        })
                        .then(blob => {
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.setAttribute('download', 'user_attendance_export_' + new Date().toISOString().slice(0, 10) + '.csv');
                            link.style.visibility = 'hidden';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            exportBtn.disabled = false;
                            exportBtn.innerHTML = originalHTML;
                            toastr.success('Attendance exported successfully!');
                        })
                        .catch(() => {
                            exportBtn.disabled = false;
                            exportBtn.innerHTML = originalHTML;
                            toastr.error('Export failed. Please try again.');
                        });
                });
            }

            document
                .querySelector('[data-kt-user-table-select="delete_selected"]')
                ?.addEventListener('click', () => {
                    const ids = [...table.querySelectorAll('.row-checkbox:checked')]
                        .map(cb => cb.value);

                    if (!ids.length) {
                        Swal.fire({
                            text: "Please select at least one attendance record.",
                            icon: "info"
                        });
                        return;
                    }

                    Swal.fire({
                        text: `Delete ${ids.length} selected attendance record(s)?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete"
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        fetch('{{ route("admin.user_attendance.deleteMultiple") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ids})
                        })
                            .then(res => {
                                if (!res.ok) throw new Error('Request failed');
                                return res.json().catch(() => ({}));
                            })
                            .then(data => {

                                toastr.success(data.message ?? 'Selected attendance records deleted successfully!');

                                datatable.draw(false);
                            })
                            .catch(error => {

                                toastr.error(error.message ?? 'Failed to delete selected records.');

                            });
                    });
                });

            return {
                init: function () {
                    if (!table) return;
                    initUserTable();
                    initToggleToolbar();
                    handleSearchDatatable();
                    handleExport();

                    window.setInterval(() => {
                        if (!document.hidden && datatable) {
                            datatable.ajax.reload(null, false);
                        }
                    }, 30000);

                    document.querySelector('[data-kt-user-table-filter="filter"]')
                        ?.addEventListener('click', () => datatable.draw());

                    document.querySelector('[data-kt-user-table-filter="reset"]')
                        ?.addEventListener('click', () => {
                            document.querySelector('[data-kt-user-table-filter="search"]').value = '';
                            if (isAdmin) {
                                const eventEl = document.querySelector('[data-kt-user-table-filter="event"]');
                                if (eventEl) {
                                    eventEl.value = '';
                                    $(eventEl).val(null).trigger('change');
                                }
                            }
                            datatable.draw();
                        });
                }
            }
        }();

        KTUtil.onDOMContentLoaded(function () {
            KTUserAttendanceList.init();
        });
    </script>
@endpush
