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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"/>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"/>
                                    </svg>
                                </span>
                                <input type="text" data-kt-user-table-filter="search"
                                       class="form-control form-control-solid w-250px ps-14"
                                       placeholder="Search user"/>
                            </div>
                        </div>

                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end align-items-center d-none"
                                 data-kt-user-table-toolbar="selected">
                                <div class="fw-bolder me-5">
                                    <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                                </div>
                                <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">
                                    Delete Selected
                                </button>
                            </div>
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
                                               value="1" />
                                    </div>
                                </th>
                                @foreach($activeFields as $field)
                                    <th class="min-w-150px">{{ $field->label }}</th>
                                @endforeach
                                <th class="min-w-150px">Session Time (Minutes)</th>
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
    <script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    <script>
        "use strict";

        var KTUserAttendanceList = function () {
            var table = document.getElementById('kt_table_user_attendance');
            var datatable;
            var toolbarBase;
            var toolbarSelected;
            var selectedCount;

            var initUserTable = function () {
                const activeFields = @json($activeFields->pluck('field_name'));

                const columns = [
                    {
                        data: 'attendance_id',
                        orderable: false,
                        searchable: false,
                        render: id => `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input row-checkbox" type="checkbox" value="${id}" />
                            </div>`
                    }
                ];

                activeFields.forEach(fieldName => {
                    columns.push({
                        data: fieldName,
                        render: data => data || '-'
                    });
                });

                columns.push(
                    {
                        data: 'session_time',
                        render: time => time ? `${time} min` : '0 min'
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
                        }
                    },
                    order: [[activeFields.length + 2, 'desc']],
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
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () {
                            toastr.success('Attendance record has been deleted!');
                            datatable.draw(false);
                        },
                        error: function() {
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
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids })
                        })
                            .then(res => res.json())
                            .then(() => {
                                toastr.success('Selected attendance records deleted successfully!');
                                datatable.draw(false);
                            })
                            .catch(() => {
                                toastr.error('Failed to delete selected records.');
                            });
                    });
                });

            return {
                init: function () {
                    if (!table) return;
                    initUserTable();
                    initToggleToolbar();
                    handleSearchDatatable();
                }
            }
        }();

        KTUtil.onDOMContentLoaded(function () {
            KTUserAttendanceList.init();
        });
    </script>
@endpush
