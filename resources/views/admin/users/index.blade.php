@extends('layouts.master')

@section('title', $title ?? '')

@section('body')
    @include('partials.header')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"/>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"/>
                                    </svg>
                                </span>
                                <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search user"/>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->

                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Filter-->

                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="filter-menu">

                                    <div id="dynamic-filters"></div>

                                </div>
                                <!--end::Menu-->
                                <!--end::Filter-->

                                <!--begin::Export-->
                                <button type="button" class="btn btn-light-primary me-3" id="export-btn">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"/>
                                            <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"/>
                                            <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"/>
                                        </svg>
                                    </span>
                                    Export
                                </button>
                                <!--end::Export-->

                                <!--begin::Add user-->
                                <a href="{{ route('user.create') }}" class="btn btn-primary">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black"/>
                                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black"/>
                                        </svg>
                                    </span>
                                    Add User
                                </a>
                                <!--end::Add user-->
                            </div>
                            <!--end::Toolbar-->

                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                                <div class="fw-bolder me-5">
                                    <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                                </div>
                                <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">
                                    Delete Selected
                                </button>
                            </div>
                            <!--end::Group actions-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                            <!--begin::Table head-->
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1"/>
                                    </div>
                                </th>
                                <th class="min-w-125px">ID</th>
                                @php
                                    $nameFieldShown = false;
                                    // Fields to exclude from table
                                    $excludeFields = ['city', 'state', 'country'];
                                @endphp
                                @foreach($activeFields as $field)
                                    @if(!in_array($field->field_name, $excludeFields))
                                        @if(in_array($field->field_name, ['first_name', 'last_name']))
                                            @if(!$nameFieldShown)
                                                <th class="min-w-125px">Name</th>
                                                @php $nameFieldShown = true; @endphp
                                            @endif
                                        @else
                                            <th class="min-w-125px">{{ $field->label }}</th>
                                        @endif
                                    @endif
                                @endforeach
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                            </thead>
                            <!--end::Table head-->

                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-bold">
                            @forelse($users as $user)
                                <tr>
                                    <!--begin::Checkbox-->
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="{{ $user->id }}"/>
                                        </div>
                                    </td>
                                    <!--end::Checkbox-->
                                    <td>{{ $loop->iteration }}</td>
                                    <!--begin::Dynamic Fields-->
                                    @php
                                        $nameFieldDisplayed = false;
                                    @endphp
                                    @foreach($activeFields as $field)
                                        @php
                                            $fieldName = $field->field_name;

                                            // Skip excluded fields
                                            if (in_array($fieldName, $excludeFields)) {
                                                continue;
                                            }

                                            // Skip last_name if first_name was already shown
                                            if ($fieldName == 'last_name' && $nameFieldDisplayed) {
                                                continue;
                                            }

                                            // Field name mapping
                                            $fieldMapping = [
                                                'mobile_number' => 'mobile',
                                                'alternative_mobile_number' => 'alternative_mobile',
                                            ];

                                            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;
                                            $value = $user->$dbFieldName;
                                        @endphp

                                        @if(in_array($fieldName, ['first_name', 'last_name']))
                                            @if(!$nameFieldDisplayed)
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: '—' }}
                                                    </div>
                                                </td>
                                                @php $nameFieldDisplayed = true; @endphp
                                            @endif
                                        @elseif($fieldName == 'name')
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: '—' }}
                                                </div>
                                            </td>
                                        @elseif($fieldName == 'email' && !empty($value))
                                            <td>
                                                <a href="mailto:{{ $value }}" class="text-gray-800 text-hover-primary">{{ $value }}</a>
                                            </td>
                                        @elseif(in_array($fieldName, ['mobile_number', 'alternative_mobile_number']) && !empty($value))
                                            <td>
                                                <a href="tel:{{ $value }}" class="text-gray-800 text-hover-primary">{{ $value }}</a>
                                            </td>
                                        @elseif($fieldName == 'avatar' && !empty($value))
                                            <td>
                                                <div class="symbol symbol-circle symbol-35px">
                                                    <img src="{{ asset('storage/' . $value) }}" alt="Avatar"/>
                                                </div>
                                            </td>
                                        @else
                                            <td>
                                                @if(!empty($value))
                                                    {{ $value }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                    <!--end::Dynamic Fields-->

                                    <!--begin::Action-->
                                    <td class="text-end">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Actions
                                            <span class="svg-icon svg-icon-5 m-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black"/>
                                                    </svg>
                                                </span>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <a href="{{ route('user.show', $user->id) }}" class="menu-link px-3">View</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="{{ route('user.edit', $user->id) }}" class="menu-link px-3">Edit</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="javascript:void(0)" onclick="deleteUser({{ $user->id }})" class="menu-link px-3 text-danger">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                    <!--end::Action-->
                                </tr>
                            @empty
                                <tr>
                                    @php
                                        $totalColumns = 3; // ID + Checkbox + Actions
                                        $nameFieldCounted = false;
                                        foreach($activeFields as $field) {
                                            if (in_array($field->field_name, $excludeFields)) {
                                                continue;
                                            }
                                            if (in_array($field->field_name, ['first_name', 'last_name'])) {
                                                if (!$nameFieldCounted) {
                                                    $totalColumns++;
                                                    $nameFieldCounted = true;
                                                }
                                            } else {
                                                $totalColumns++;
                                            }
                                        }
                                    @endphp
                                    <td colspan="{{ $totalColumns }}" class="text-center py-5">No users found</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>

    <!--begin::View User Modal-->
    <div class="modal fade" id="kt_modal_view_user" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_view_user_header">
                    <h2 class="fw-bolder">User Details</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"/>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div id="user-details-content">
                        <div class="text-center">
                            <span class="spinner-border spinner-border-lg align-middle"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::View User Modal-->

    @include('partials.footer')
@endsection


@push('scripts')
    <script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>

    <script>
        "use strict";

        // Pass dynamic fields from backend to JavaScript
        const dynamicFields = @json($activeFields->map(function($field) {
            return [
                'field_name' => $field->field_name,
                'label' => $field->label
            ];
        }));

        // Fields to exclude from listing
        const excludeFields = ['city', 'state', 'country'];

        var KTUsersList = function () {
            var table = document.getElementById('kt_table_users');
            var datatable;
            var toolbarBase;
            var toolbarSelected;
            var selectedCount;

            // Initialize dynamic filters
            var initDynamicFilters = function() {
                const filterContainer = document.getElementById('dynamic-filters');

                dynamicFields.forEach(field => {
                    // Skip excluded fields
                    if (excludeFields.includes(field.field_name)) {
                        return;
                    }

                    const filterDiv = document.createElement('div');
                    filterDiv.className = 'mb-5';
                    filterDiv.innerHTML = `
                        <label class="form-label fs-6 fw-bold">${field.label}:</label>
                        <input type="text"
                               class="form-control form-control-solid"
                               data-filter-field="${field.field_name}"
                               placeholder="Search by ${field.label}">
                    `;
                    filterContainer.appendChild(filterDiv);
                });
            };

            // Initialize datatable
            var initUserTable = function () {
                datatable = $(table).DataTable({
                    searchDelay: 500,
                    processing: true,
                    order: [[1, 'asc']],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [0, -1] // First and last column
                        }
                    ]
                });

                datatable.on('draw', function () {
                    initToggleToolbar();
                    toggleToolbars();
                    KTMenu.createInstances();
                });
            }

            // Search Datatable
            var handleSearchDatatable = function () {
                const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
                if (filterSearch) {
                    filterSearch.addEventListener('keyup', function (e) {
                        datatable.search(e.target.value).draw();
                    });
                }
            }

            // Handle filter
            var handleFilterDatatable = function () {
                const filterButton = document.querySelector('[data-kt-user-table-filter="filter"]');
                const resetButton = document.querySelector('[data-kt-user-table-filter="reset"]');

                if (filterButton) {
                    filterButton.addEventListener('click', function () {
                        const filters = document.querySelectorAll('[data-filter-field]');
                        let filterString = '';

                        filters.forEach(filter => {
                            const value = filter.value;
                            if (value) {
                                filterString += value + ' ';
                            }
                        });

                        datatable.search(filterString.trim()).draw();
                    });
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        const filters = document.querySelectorAll('[data-filter-field]');
                        filters.forEach(filter => {
                            filter.value = '';
                        });
                        datatable.search('').draw();
                    });
                }
            }

            // Handle export
            var handleExport = function () {
                const exportBtn = document.getElementById('export-btn');

                if (exportBtn) {
                    exportBtn.addEventListener('click', function () {
                        const originalHTML = exportBtn.innerHTML;
                        exportBtn.disabled = true;
                        exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';

                        let csv = '';

                        // Add headers (excluding city, state, country)
                        const headers = dynamicFields
                            .filter(f => !excludeFields.includes(f.field_name))
                            .map(f => f.label);
                        csv += headers.join(',') + '\n';

                        // Add rows
                        const rows = document.querySelectorAll('#kt_table_users tbody tr');
                        rows.forEach(row => {
                            const cells = row.querySelectorAll('td');
                            if (cells.length > 1) {
                                let rowData = [];
                                for (let i = 1; i < cells.length - 1; i++) {
                                    const text = cells[i].innerText.trim().replace(/\n/g, ' ');
                                    rowData.push(`"${text}"`);
                                }
                                csv += rowData.join(',') + '\n';
                            }
                        });

                        // Download
                        const blob = new Blob([csv], { type: 'text/csv' });
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.setAttribute('href', url);
                        link.setAttribute('download', 'users_export.csv');
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        setTimeout(() => {
                            exportBtn.disabled = false;
                            exportBtn.innerHTML = originalHTML;

                            Swal.fire({
                                text: "Users exported successfully!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }, 500);
                    });
                }
            }

            // Delete multiple users
            var handleDeleteRows = () => {
                const deleteButton = document.querySelector('[data-kt-user-table-select="delete_selected"]');

                if (!deleteButton) return;

                deleteButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    const checkboxes = table.querySelectorAll('tbody [type="checkbox"]:checked');

                    if (checkboxes.length > 0) {
                        Swal.fire({
                            text: "Are you sure you want to delete selected users?",
                            icon: "warning",
                            showCancelButton: true,
                            buttonsStyling: false,
                            confirmButtonText: "Yes, delete!",
                            cancelButtonText: "No, cancel",
                            customClass: {
                                confirmButton: "btn fw-bold btn-danger",
                                cancelButton: "btn fw-bold btn-active-light-primary"
                            }
                        }).then(function (result) {
                            if (result.value) {
                                const ids = [];
                                checkboxes.forEach(checkbox => {
                                    ids.push(checkbox.value);
                                });

                                $.ajax({
                                    url: '{{ route("user.deleteMultiple") }}',
                                    method: 'POST',
                                    data: {
                                        ids: ids,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            text: "You have deleted selected users!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        }).then(function() {
                                            location.reload();
                                        });
                                    },
                                    error: function(xhr) {
                                        Swal.fire({
                                            text: "Error deleting users. Please try again.",
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            }

            // Toggle toolbars
            var toggleToolbars = function () {
                toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
                toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
                selectedCount = document.querySelector('[data-kt-user-table-select="selected_count"]');

                const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');

                let checkedState = false;
                let count = 0;

                allCheckboxes.forEach(c => {
                    if (c.checked) {
                        checkedState = true;
                        count++;
                    }
                });

                if (checkedState) {
                    selectedCount.innerHTML = count;
                    toolbarBase.classList.add('d-none');
                    toolbarSelected.classList.remove('d-none');
                } else {
                    toolbarBase.classList.remove('d-none');
                    toolbarSelected.classList.add('d-none');
                }
            };

            // Init toggle toolbar
            var initToggleToolbar = function () {
                const checkboxes = table.querySelectorAll('tbody [type="checkbox"]');
                const headerCheckbox = table.querySelector('thead [type="checkbox"]');

                if (headerCheckbox) {
                    headerCheckbox.addEventListener('change', function (e) {
                        checkboxes.forEach(c => {
                            c.checked = e.target.checked;
                        });
                        toggleToolbars();
                    });
                }

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        toggleToolbars();

                        const allChecked = Array.from(checkboxes).every(c => c.checked);
                        const anyChecked = Array.from(checkboxes).some(c => c.checked);

                        if (headerCheckbox) {
                            headerCheckbox.checked = allChecked;
                            headerCheckbox.indeterminate = anyChecked && !allChecked;
                        }
                    });
                });
            };

            return {
                init: function () {
                    if (!table) return;

                    initDynamicFilters();
                    initUserTable();
                    initToggleToolbar();
                    handleSearchDatatable();
                    handleFilterDatatable();
                    handleExport();
                    handleDeleteRows();
                }
            }
        }();

        // View user details
        function viewUser(id) {
            const modal = new bootstrap.Modal(document.getElementById('kt_modal_view_user'));
            const contentDiv = document.getElementById('user-details-content');

            // Show loading
            contentDiv.innerHTML = '<div class="text-center"><span class="spinner-border spinner-border-lg align-middle"></span></div>';

            modal.show();

            // Fetch user data
            $.ajax({
                url: '{{ route("user.show", ":id") }}'.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    let html = '';
                    let nameFieldDisplayed = false;

                    // Check if avatar field is active and show it
                    const avatarField = dynamicFields.find(f => f.field_name === 'avatar');
                    if (avatarField && response.avatar) {
                        html += `
                            <div class="text-center mb-8">
                                <div class="symbol symbol-100px symbol-circle mb-5">
                                    <img src="${response.avatar}" alt="Avatar"/>
                                </div>
                            </div>
                        `;
                    }

                    // All active fields
                    html += '<div class="row g-5">';

                    dynamicFields.forEach(field => {
                        const fieldName = field.field_name;

                        // Field name mapping for database columns
                        const fieldMapping = {
                            'mobile_number': 'mobile',
                            'alternative_mobile_number': 'alternative_mobile'
                        };

                        const dbFieldName = fieldMapping[fieldName] || fieldName;
                        let value = response[dbFieldName] || '—';

                        // Handle name fields - combine first_name and last_name
                        if (fieldName === 'first_name' || fieldName === 'last_name') {
                            if (!nameFieldDisplayed) {
                                const fullName = `${response.first_name || ''} ${response.last_name || ''}`.trim() || '—';
                                html += `
                                    <div class="col-md-6">
                                        <div class="fw-bolder text-gray-600 mb-2">Name:</div>
                                        <div class="fw-bold fs-6 text-gray-800">${fullName}</div>
                                    </div>
                                `;
                                nameFieldDisplayed = true;
                            }
                            return;
                        }

                        // Handle 'name' field explicitly
                        if (fieldName === 'name') {
                            value = `${response.first_name || ''} ${response.last_name || ''}`.trim() || '—';
                        }

                        // Skip avatar field (already shown above)
                        if (fieldName === 'avatar') {
                            return;
                        }

                        // Skip password field
                        if (fieldName === 'password') {
                            return;
                        }

                        // Format email with mailto link
                        if (fieldName === 'email' && value !== '—') {
                            value = `<a href="mailto:${value}" class="text-primary">${value}</a>`;
                        }

                        // Format mobile numbers with tel link
                        if ((fieldName === 'mobile_number' || fieldName === 'alternative_mobile_number') && value !== '—') {
                            value = `<a href="tel:${value}" class="text-primary">${value}</a>`;
                        }

                        html += `
                            <div class="col-md-6">
                                <div class="fw-bolder text-gray-600 mb-2">${field.label}:</div>
                                <div class="fw-bold fs-6 text-gray-800">${value}</div>
                            </div>
                        `;
                    });

                    html += '</div>';

                    contentDiv.innerHTML = html;
                },
                error: function(xhr) {
                    contentDiv.innerHTML = '<div class="alert alert-danger">Error loading user details. Please try again.</div>';
                }
            });
        }

        // Delete single user
        function deleteUser(id) {
            Swal.fire({
                text: "Are you sure you want to delete this user?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ route("user.destroy", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                text: "User has been deleted!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                text: "Error deleting user. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    });
                }
            });
        }

        KTUtil.onDOMContentLoaded(function () {
            KTUsersList.init();
        });
    </script>
@endpush
