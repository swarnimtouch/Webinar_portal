@extends('layouts.master')

@section($title,'title')

@section('body')
    @include('partials.header')
    <!--begin::Content-->
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                              height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                                              fill="black"/>
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="black"/>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" data-kt-user-table-filter="search"
                                       class="form-control form-control-solid w-250px ps-14"
                                       placeholder="Search fields"/>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end">
                                <!-- Toolbar empty - button moved to footer -->
                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <form id="fields-form" method="POST" action="{{ route('dynamic-fields.store') }}">
                            @csrf

                            <input type="hidden" name="order_data" id="order-data">

                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-80px">Move</th>
                                    <th class="min-w-125px">Index</th>
                                    <th class="min-w-150px">Field Name</th>
                                    <th class="min-w-150px">Label</th>
                                    <th class="min-w-150px">Required</th>
                                    <th class="min-w-150px">Status</th>
                                    <th class="min-w-150px">Login With</th>
                                </tr>
                                </thead>

                                <tbody class="text-gray-600 fw-bold">
                                @forelse($fields as $field)
                                    <tr data-id="{{ $field->id }}">
                                        <!-- Drag Handle -->
                                        <td class="drag-handle" style="cursor:move;font-size:18px">☰</td>

                                        <!-- Index -->
                                        <td>
                                            <span class="badge bg-secondary index-badge">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>

                                        <!-- Field Name -->
                                        <td>{{ $field->field_name }}</td>

                                        <!-- Label -->
                                        <td>
                                            <input type="text"
                                                   name="fields[{{ $field->id }}][label]"
                                                   value="{{ $field->label }}"
                                                   class="form-control form-control-sm">
                                        </td>

                                        <!-- Required -->
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

                                        <!-- Status -->
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

                                        <!-- Login With -->
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
                                                    <input type="hidden"
                                                           name="password_required"
                                                           value="0">
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">No Dynamic Fields found</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </form>
                    </div>
                    <!--end::Card body-->

                    <!--begin::Card footer-->
                    <div class="card-footer d-flex justify-content-end py-6">
                        <button type="button" id="save-fields-btn" class="btn btn-primary">
                            <span class="svg-icon svg-icon-2">

                            </span>
                            Save Changes
                        </button>
                    </div>
                    <!--end::Card footer-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
    @include('partials.footer')
@endsection

@push('scripts')
    <script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        "use strict";

        var KTUsersList = function () {
            var table = document.getElementById('kt_table_users');
            var datatable;

            // Initialize datatable
            var initUserTable = function () {
                datatable = $(table).DataTable({
                    searchDelay: 500,
                    processing: true,
                    ordering: false, // Disable ordering since using manual drag-drop
                    stateSave: false,
                    paging: false, // Disable pagination for sortable to work properly
                    info: false, // Hide "Showing X of Y entries"
                    columnDefs: [
                        {
                            orderable: false,
                            targets: '_all'
                        }
                    ]
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

            return {
                init: function () {
                    if (!table) {
                        return;
                    }

                    initUserTable();
                    handleSearchDatatable();
                }
            }
        }();

        // Sortable functionality
        function initSortable() {
            function updateIndexes() {
                let order = [];

                $("#kt_table_users tbody tr").each(function (index) {
                    $(this).find('.index-badge').text(index + 1);

                    const rowId = $(this).data('id');
                    if (rowId) {
                        order.push({
                            id: rowId,
                            index_no: index + 1
                        });
                    }
                });

                // Store order data in hidden field
                $('#order-data').val(JSON.stringify(order));
            }

            $("#kt_table_users tbody").sortable({
                handle: ".drag-handle",
                placeholder: "ui-state-highlight",
                helper: function(e, tr) {
                    var $originals = tr.children();
                    var $helper = tr.clone();
                    $helper.children().each(function(index) {
                        $(this).width($originals.eq(index).width());
                    });
                    return $helper;
                },
                update: function (event, ui) {
                    updateIndexes();
                }
            });

            // Initial index update
            updateIndexes();
        }

        // Save form handler
        function initSaveButton() {
            const saveBtn = document.getElementById('save-fields-btn');
            const form = document.getElementById('fields-form');

            if (saveBtn && form) {
                saveBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Show loading state
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                    // Submit form via AJAX
                    const formData = new FormData(form);

                    $.ajax({
                        url: form.action,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // Reset button
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = '<span class="svg-icon svg-icon-2"></span>Save Changes';

                            // Show success message
                            Swal.fire({
                                text: response.message || "Changes saved successfully!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        },
                        error: function(xhr) {
                            // Reset button
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = '<span class="svg-icon svg-icon-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="black"/><path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="black"/></svg></span>Save Changes';

                            // Show error message
                            Swal.fire({
                                text: xhr.responseJSON?.message || "Error saving changes. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    });
                });
            }
        }

        // On document ready
        KTUtil.onDOMContentLoaded(function () {
            KTUsersList.init();
            initSortable();
            initSaveButton();
        });
    </script>

    <style>
        .ui-state-highlight {
            height: 50px;
            background-color: #f3f6f9;
            border: 2px dashed #3699ff;
        }

        .drag-handle:hover {
            color: #3699ff;
        }

        #kt_table_users tbody tr {
            transition: background-color 0.2s;
        }

        #kt_table_users tbody tr.ui-sortable-helper {
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
@endpush
