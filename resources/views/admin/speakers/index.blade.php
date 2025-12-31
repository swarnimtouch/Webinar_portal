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
                                       class="form-control form-control-solid w-250px ps-14" placeholder="Search speaker"/>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Add user-->
                                <a href="{{ route('speaker.create') }}" class="btn btn-primary">
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
                                    Add Speaker
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
                                               data-kt-check-target="#kt_table_users .form-check-input"
                                               value="1" />
                                    </div>
                                </th>
                                <th class="min-w-80px">ID</th>
                                <th class="min-w-125px">Photo</th>
                                <th class="min-w-150px">Name</th>
                                <th class="min-w-150px">Line 1</th>
                                <th class="min-w-150px">Line 2</th>
                                <th class="min-w-150px">Line 3</th>
                                <th class="min-w-150px">Created At</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                            @forelse($speakers as $speaker)
                                <tr>
                                    <!-- Checkbox -->
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="{{ $speaker->id }}" />
                                        </div>
                                    </td>

                                    <!-- ID -->
                                    <td>{{ $loop->iteration }}</td>

                                    <!-- Photo -->
                                    <td>
                                        @if($speaker->media_url)
                                            <div class="position-relative overflow-hidden rounded"
                                                 style="width:80px;height:80px;cursor:pointer;"
                                                 onclick="showBannerPreview(
                                                    '{{ $speaker->media_url }}',
                                                    'image',
                                                    'Speakers'
                                                 )">
                                                <img src="{{ $speaker->media_url }}"
                                                     alt="Speaker"
                                                     style="width:100%;height:100%;object-fit:cover;">
                                            </div>
                                        @else
                                            <div style="width:60px;height:60px;background:#f3f6f9;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <span class="text-muted">No Image</span>
                                            </div>
                                        @endif
                                    </td>



                                    <!-- Name -->
                                    <td>{{ $speaker->name ?? '-' }}</td>

                                    <!-- Line 1 -->
                                    <td>{{ $speaker->line1 ?? '-' }}</td>

                                    <!-- Line 2 -->
                                    <td>{{ $speaker->line2 ?? '-' }}</td>

                                    <!-- Line 3 -->
                                    <td>{{ $speaker->line3 ?? '-' }}</td>

                                    <!-- Created -->
                                    <td>{{ $speaker->created_at->format('d M Y') }}</td>

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
                                                <a href="{{ route('speaker.create', $speaker->id) }}" class="menu-link px-3">Edit</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="javascript:void(0)" onclick="deleteSpeaker({{ $speaker->id }})" class="menu-link px-3 text-danger">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Actions -->
{{--                                    <td class="text-end">--}}
{{--                                        <a href="{{ route('speaker.create', $speaker->id) }}"--}}
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
{{--                                                onclick="deleteSpeaker({{ $speaker->id }})"--}}
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
                                    <td colspan="8" class="text-center py-5">No speakers found</td>
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
        <!--end::Post-->
    </div>
    <div class="modal fade" id="bannerPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerPreviewTitle">Banner Preview</h5>
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
    <!--end::Content-->
    @include('partials.footer')
@endsection

@push('scripts')
    <script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
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
                    order: [[1, 'desc']], // Order by ID column
                    stateSave: false,
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [0, 2, 8] // Disable ordering on checkbox, photo, and actions columns
                        },
                        {
                            targets: 2, // Photo column
                            searchable: false
                        }
                    ]
                });

                // Re-init functions on datatable re-draw
                datatable.on('draw', function () {
                    initToggleToolbar();
                    handleDeleteRows();
                    toggleToolbars();
                    KTMenu.createInstances();
                });
            }

            // Search Datatable
            var handleSearchDatatable = function () {
                const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
                filterSearch.addEventListener('keyup', function (e) {
                    datatable.search(e.target.value).draw();
                });
            }

            // Delete selected rows
            var handleDeleteRows = () => {
                const deleteButton = document.querySelector('[data-kt-user-table-select="delete_selected"]');

                if (!deleteButton) return;

                deleteButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    const checkboxes = table.querySelectorAll('tbody [type="checkbox"]:checked');

                    if (checkboxes.length > 0) {
                        Swal.fire({
                            text: "Are you sure you want to delete selected speakers?",
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
                                // Get selected IDs
                                const ids = [];
                                checkboxes.forEach(checkbox => {
                                    ids.push(checkbox.value);
                                });

                                // Make AJAX call to delete
                                $.ajax({
                                    url: '{{ route("speaker.deleteMultiple") }}',
                                    method: 'POST',
                                    data: {
                                        ids: ids,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        // Show success message
                                        Swal.fire({
                                            text: "You have deleted selected speakers!",
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn fw-bold btn-primary",
                                            }
                                        }).then(function() {
                                            // Reload page to reflect changes
                                            location.reload();
                                        });
                                    },
                                    error: function(xhr) {
                                        Swal.fire({
                                            text: "Error deleting speakers. Please try again.",
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
                // Target table body checkboxes
                const checkboxes = table.querySelectorAll('tbody [type="checkbox"]');

                // Select all checkbox in header
                const headerCheckbox = table.querySelector('thead [type="checkbox"]');

                // Handle header checkbox click
                if (headerCheckbox) {
                    headerCheckbox.addEventListener('change', function (e) {
                        checkboxes.forEach(c => {
                            c.checked = e.target.checked;
                        });
                        toggleToolbars();
                    });
                }

                // Handle individual checkbox clicks
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        toggleToolbars();

                        // Update header checkbox state
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
                    if (!table) {
                        return;
                    }

                    initUserTable();
                    initToggleToolbar();
                    handleSearchDatatable();
                    handleDeleteRows();
                }
            }
        }();

        // Delete single speaker
        function deleteSpeaker(id) {
            Swal.fire({
                text: "Are you sure you want to delete this speaker?",
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
                    // Make AJAX call to delete
                    $.ajax({
                        url: '{{ route("speaker.delete", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                text: "Speaker has been deleted!",
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
                                text: "Error deleting speaker. Please try again.",
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
    <script>
        function showBannerPreview(fileUrl, type, titleText = 'Speakers') {

            const modal = new bootstrap.Modal(
                document.getElementById('bannerPreviewModal')
            );

            const modalTitle = document.getElementById('bannerPreviewTitle');
            const modalBody  = document.getElementById('bannerPreviewBody');

            // ✅ Title dynamically set hoga
            modalTitle.textContent = titleText;

            modalBody.innerHTML = '';

            if (type === 'image') {
                const img = document.createElement('img');
                img.src = fileUrl;
                img.className = 'img-fluid rounded';
                img.style.maxHeight = '70vh';
                modalBody.appendChild(img);
            }
            else if (type === 'video') {
                const video = document.createElement('video');
                video.controls = true;
                video.autoplay = true;
                video.className = 'rounded';
                video.style.width = '100%';
                video.style.maxHeight = '70vh';
                video.id = 'modalVideoPlayer';

                const source = document.createElement('source');
                source.src = fileUrl;
                source.type = 'video/mp4';

                video.appendChild(source);
                modalBody.appendChild(video);
            }

            modal.show();

            // Close par video stop
            document
                .getElementById('bannerPreviewModal')
                .addEventListener('hidden.bs.modal', function () {
                    const videoPlayer = document.getElementById('modalVideoPlayer');
                    if (videoPlayer) {
                        videoPlayer.pause();
                        videoPlayer.currentTime = 0;
                        videoPlayer.src = '';
                    }
                }, { once: true });
        }
    </script>

@endpush
