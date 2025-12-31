
@extends('layouts.master')

@section($title,'title')

@section('body')
    @include('partials.header')

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->

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
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
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
                                       class="form-control form-control-solid w-250px ps-14" placeholder="Search brands"/>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Filter-->
                                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                                        data-kt-menu-placement="bottom-end">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                    <span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                         viewBox="0 0 24 24" fill="none">
														<path
                                                            d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                            fill="black"/>
													</svg>
												</span>
                                    <!--end::Svg Icon-->Filter
                                </button>
                                <!--begin::Menu 1-->
                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                    <!--begin::Header-->
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Separator-->
                                    <div class="separator border-gray-200"></div>
                                    <!--end::Separator-->
                                    <!--begin::Content-->
                                    <div class="px-7 py-5" data-kt-user-table-filter="form">
                                        <!--begin::Input group-->
                                        <div class="mb-10">
                                            <label class="form-label fs-6 fw-bold">Status:</label>
                                            <select class="form-select form-select-solid fw-bolder" data-kt-select2="true"
                                                    data-placeholder="Select option" data-allow-clear="true"
                                                    data-kt-user-table-filter="status" data-hide-search="true">
                                                <option></option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="d-flex justify-content-end">
                                            <button type="reset"
                                                    class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                    data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary fw-bold px-6"
                                                    data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply
                                            </button>
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Menu 1-->
                                <!--end::Filter-->
                                <!--begin::Add user-->
                                <a href="{{ route('brand.create') }}" class="btn btn-primary">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                              rx="1" transform="rotate(-90 11.364 20.364)"
                                              fill="black"/>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                              fill="black"/>
                                    </svg>
                                </span>
                                    Add Brands
                                </a>

                                <!--end::Add user-->
                            </div>
                            <!--end::Toolbar-->
                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none"
                                 data-kt-user-table-toolbar="selected">
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
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_users .row-checkbox"/>
                                    </div>
                                </th>
                                <th class="min-w-80px">ID</th>
                                <th class="min-w-125px">Preview</th>
                                <th class="min-w-200px">Title</th>
                                <th class="min-w-150px">Created At</th>
                                <th class="min-w-100px">Status</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                            @forelse($brands as $brand)
                                <tr>
                                    <!-- Checkbox -->
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $brand->id }}" />
                                        </div>
                                    </td>

                                    <!-- ID -->
                                    <td>{{ $loop->iteration }}</td>


                                    <!-- Preview -->
                                    <td>
                                        @if($brand->type === 'image' && $brand->media_url)
                                            <div class="position-relative overflow-hidden rounded"
                                                 style="width:80px;height:80px;cursor:pointer;"
                                                 onclick="showBannerPreview(
                                                    '{{ $brand->media_url }}',
                                                    'image'
                                                 )">
                                                <img src="{{ $brand->media_url }}"
                                                     alt="Brand"
                                                     style="width:100%;height:100%;object-fit:cover;">
                                            </div>
                                        @elseif($brand->type === 'video' && $brand->media_url)
                                            <video width="100" height="60" muted>
                                                <source src="{{ $brand->media_url }}" type="video/mp4">
                                            </video>
                                        @else
                                            <span class="text-muted">No Media</span>
                                        @endif
                                    </td>

                                    <!-- Filename -->
                                    <td>{{ $brand->title ?? '-' }}</td>

                                    <!-- Created -->
                                    <td>{{ $brand->created_at->format('d M Y') }}</td>

                                    <!-- Status -->
                                    <td>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input banner-status-switch"
                                                   type="checkbox"
                                                   {{ $brand->status === 'active' ? 'checked' : '' }}
                                                   onclick="toggleStatus({{ $brand->id }}, this);"
                                                   id="status_{{ $brand->id }}">
                                        </div>
                                    </td>
                                    <!-- Actions -->

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
                                                {{--                                                <a href="{{ route('user.show', $user->id) }}" class="menu-link px-3">View</a>--}}
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="{{ route('brand.create', $brand->id) }}" class="menu-link px-3">Edit</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="javascript:void(0)" onclick="deleteBrand({{ $brand->id }})" class="menu-link px-3 text-danger">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Actions -->

{{--                                    <td class="text-end">--}}
{{--                                        <a href="{{ route('brand.create', $brand->id) }}"--}}
{{--                                           class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"--}}
{{--                                           title="Edit">--}}
{{--                                            <span class="svg-icon svg-icon-3">--}}
{{--                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black"/>--}}
{{--                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black"/>--}}
{{--                                                </svg>--}}
{{--                                            </span>--}}
{{--                                        </a>--}}
{{--                                        <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"--}}
{{--                                                onclick="deleteBrand({{ $brand->id }})"--}}
{{--                                                title="Delete">--}}
{{--                                            <span class="svg-icon svg-icon-3">--}}
{{--                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"/>--}}
{{--                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"/>--}}
{{--                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"/>--}}
{{--                                                </svg>--}}
{{--                                            </span>--}}
{{--                                        </button>--}}
{{--                                    </td>--}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">No brands found</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <div class="modal fade" id="bannerPreviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bannerPreviewTitle">Brand Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" id="bannerPreviewBody">
                        <!-- Preview content will be inserted here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Post-->
        @include('partials.footer')
        @endsection

        @push('scripts')
            <script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
            <script>
                function showBannerPreview(fileUrl, type) {
                    const modal = new bootstrap.Modal(document.getElementById('bannerPreviewModal'));
                    const modalTitle = document.getElementById('bannerPreviewTitle');
                    const modalBody = document.getElementById('bannerPreviewBody');

                    // Set title
                    modalTitle.textContent = 'Brands';

                    // Clear previous content
                    modalBody.innerHTML = '';

                    if (type === 'image') {
                        // Create image element
                        const img = document.createElement('img');
                        img.src = fileUrl;
                        img.className = 'img-fluid rounded';
                        img.style.maxHeight = '70vh';
                        modalBody.appendChild(img);
                    } else if (type === 'video') {
                        // Create video element
                        const video = document.createElement('video');
                        video.controls = true;
                        video.autoplay = true;
                        video.className = 'rounded';
                        video.style.maxHeight = '70vh';
                        video.style.width = '100%';
                        video.id = 'modalVideoPlayer';

                        const source = document.createElement('source');
                        source.src = fileUrl;
                        source.type = 'video/mp4';

                        video.appendChild(source);
                        modalBody.appendChild(video);
                    }

                    // Show modal
                    modal.show();

                    // Modal close hone par video pause aur sound band karo
                    const modalElement = document.getElementById('bannerPreviewModal');
                    modalElement.addEventListener('hidden.bs.modal', function () {
                        const videoPlayer = document.getElementById('modalVideoPlayer');
                        if (videoPlayer) {
                            videoPlayer.pause();
                            videoPlayer.currentTime = 0;
                            videoPlayer.src = '';
                        }
                    });
                }
            </script>
            <script>
                "use strict";

                var KTUsersList = function () {
                    var table = document.getElementById('kt_table_users');
                    var datatable;
                    var toolbarBase;
                    var toolbarSelected;
                    var selectedCount;

                    // Initialize datatable
                    var initUserTable = function () {
                        datatable = $(table).DataTable({
                            searchDelay: 500,
                            processing: true,
                            order: [[1, 'desc']],
                            stateSave: false,
                            columnDefs: [
                                {
                                    orderable: false,
                                    targets: [0, 2, 6]
                                }
                            ]
                        });

                        // Re-init functions on datatable re-draw
                        datatable.on('draw', function () {
                            initToggleToolbar();
                            handleDeleteRows();
                            toggleToolbars();
                            handleSelectAllCheckbox(); // Re-init after draw
                        });
                    }

                    // Handle "Select All" checkbox
                    var handleSelectAllCheckbox = function() {
                        const selectAllCheckbox = table.querySelector('thead .form-check-input');

                        if (selectAllCheckbox) {
                            // Remove existing listener to avoid duplicates
                            selectAllCheckbox.removeEventListener('change', selectAllHandler);
                            selectAllCheckbox.addEventListener('change', selectAllHandler);
                        }
                    };

                    // Select all handler function
                    function selectAllHandler() {
                        const isChecked = this.checked;
                        const rowCheckboxes = table.querySelectorAll('tbody .row-checkbox');

                        rowCheckboxes.forEach(checkbox => {
                            checkbox.checked = isChecked;
                        });

                        toggleToolbars();
                    }

                    // Search Datatable
                    var handleSearchDatatable = function () {
                        const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
                        filterSearch.addEventListener('keyup', function (e) {
                            datatable.search(e.target.value).draw();
                        });
                    }

                    // Filter Datatable
                    var handleFilterDatatable = () => {
                        const filterButton = document.querySelector('[data-kt-user-table-filter="filter"]');
                        const resetButton = document.querySelector('[data-kt-user-table-filter="reset"]');
                        const statusFilter = document.querySelector('[data-kt-user-table-filter="status"]');

                        // Filter datatable on submit
                        filterButton.addEventListener('click', function () {
                            var statusValue = statusFilter.value.toLowerCase();

                            // Clear any existing custom search
                            $.fn.dataTable.ext.search = [];

                            // Apply status filter
                            if (statusValue !== '') {
                                $.fn.dataTable.ext.search.push(
                                    function(settings, data, dataIndex) {
                                        var statusCell = $(table).DataTable().row(dataIndex).node();
                                        var statusCheckbox = $(statusCell).find('.banner-status-switch');
                                        var isActive = statusCheckbox.is(':checked');
                                        var currentStatus = isActive ? 'active' : 'inactive';

                                        return statusValue === '' || currentStatus === statusValue;
                                    }
                                );
                            }

                            datatable.draw();
                        });

                        // Reset datatable
                        resetButton.addEventListener('click', function () {
                            $(statusFilter).val('').trigger('change');
                            $.fn.dataTable.ext.search = [];
                            datatable.search('').draw();
                        });
                    }

                    // Delete selected rows
                    var handleDeleteRows = () => {
                        const deleteButton = document.querySelector('[data-kt-user-table-select="delete_selected"]');

                        deleteButton.addEventListener('click', function () {
                            const checkboxes = table.querySelectorAll('tbody .row-checkbox:checked');

                            if (checkboxes.length > 0) {
                                Swal.fire({
                                    text: "Are you sure you want to delete selected brands?",
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
                                            url: '{{ route("brand.deleteMultiple") }}',
                                            method: 'POST',
                                            data: {
                                                ids: ids,
                                                _token: '{{ csrf_token() }}'
                                            },
                                            success: function(response) {
                                                Swal.fire({
                                                    text: "You have deleted selected brands!",
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
                                                    text: "Error deleting brands. Please try again.",
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

                        const allCheckboxes = table.querySelectorAll('tbody .row-checkbox');
                        const selectAllCheckbox = table.querySelector('thead .form-check-input');

                        let checkedState = false;
                        let count = 0;

                        allCheckboxes.forEach(c => {
                            if (c.checked) {
                                checkedState = true;
                                count++;
                            }
                        });

                        // Update select all checkbox state
                        if (selectAllCheckbox) {
                            if (count === 0) {
                                selectAllCheckbox.checked = false;
                                selectAllCheckbox.indeterminate = false;
                            } else if (count === allCheckboxes.length) {
                                selectAllCheckbox.checked = true;
                                selectAllCheckbox.indeterminate = false;
                            } else {
                                selectAllCheckbox.checked = false;
                                selectAllCheckbox.indeterminate = true;
                            }
                        }

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
                        const checkboxes = table.querySelectorAll('tbody .row-checkbox');

                        checkboxes.forEach(checkbox => {
                            checkbox.addEventListener('click', function () {
                                setTimeout(function () {
                                    toggleToolbars();
                                }, 50);
                            });
                        });
                    };

                    // Handle status toggle using event delegation
                    var handleStatusToggle = function() {
                        $(table).on('change', '.banner-status-switch', function() {
                            const checkbox = this;
                            const brandId = checkbox.id.replace('status_', '');
                            const previousState = !checkbox.checked;
                            const newStatus = checkbox.checked ? 'active' : 'inactive';

                            // Temporarily revert the checkbox
                            checkbox.checked = previousState;

                            Swal.fire({
                                text: "Are you sure you want to change the status?",
                                icon: "warning",
                                showCancelButton: true,
                                buttonsStyling: false,
                                confirmButtonText: "Yes, change it!",
                                cancelButtonText: "No, cancel",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                    cancelButton: "btn fw-bold btn-active-light-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    // Apply the toggle
                                    checkbox.checked = !previousState;

                                    $.ajax({
                                        url: '{{ route("brand.toggleStatus", ":id") }}'.replace(':id', brandId),
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            status: newStatus
                                        },
                                        success: function () {
                                            if (typeof toastr !== 'undefined') {
                                                toastr.success('Status updated successfully!');
                                            } else {
                                                Swal.fire({
                                                    text: "Status updated successfully!",
                                                    icon: "success",
                                                    buttonsStyling: false,
                                                    confirmButtonText: "Ok",
                                                    customClass: {
                                                        confirmButton: "btn fw-bold btn-primary",
                                                    }
                                                });
                                            }
                                        },
                                        error: function () {
                                            // Revert on error
                                            checkbox.checked = previousState;

                                            Swal.fire({
                                                text: "Error updating status. Please try again.",
                                                icon: "error",
                                                buttonsStyling: false,
                                                confirmButtonText: "Ok",
                                                customClass: {
                                                    confirmButton: "btn fw-bold btn-primary",
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        });
                    };

                    return {
                        init: function () {
                            if (!table) {
                                return;
                            }

                            initUserTable();
                            handleSelectAllCheckbox(); // Initialize select all
                            initToggleToolbar();
                            handleSearchDatatable();
                            handleFilterDatatable();
                            handleDeleteRows();
                            handleStatusToggle(); // Initialize status toggle
                        }
                    }
                }();

                // Delete single brand
                function deleteBrand(id) {
                    Swal.fire({
                        text: "Are you sure you want to delete this brand?",
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
                                url: '{{ route("brand.delete", ":id") }}'.replace(':id', id),
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        text: "Brand has been deleted!",
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
                                        text: "Error deleting brand. Please try again.",
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

                // On document ready
                KTUtil.onDOMContentLoaded(function () {
                    KTUsersList.init();
                });

            </script>
    @endpush
