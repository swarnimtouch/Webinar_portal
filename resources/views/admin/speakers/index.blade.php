@extends('layouts.admin')
@section('content')
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
                                       class="form-control form-control-solid w-250px ps-14"
                                       placeholder="Search speaker"/>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Add user-->
                                <a href="{{ route('admin.speaker.add_edit_form') }}" class="btn btn-primary">
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
                                <button type="button" class="btn btn-danger"
                                        data-kt-user-table-select="delete_selected">
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
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_speakers">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_users .form-check-input"
                                               value="1"/>
                                    </div>
                                </th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Line 1</th>
                                <th>Line 2</th>
                                <th>Line 3</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
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
@endsection

@push('scripts')
    <script>
        "use strict";

        var KTSpeakersList = function () {
            var table = document.getElementById('kt_table_speakers');
            var datatable;
            var toolbarBase;
            var toolbarSelected;
            var selectedCount;

            var initSpeakerTable = function () {
                datatable = $(table).DataTable({
                    processing: true,
                    serverSide: true,
                    searchDelay: 500,
                    ajax: {
                        url: '{{ route("admin.speaker.datatable") }}',
                        data: d => {
                            d.search = document.querySelector('[data-kt-user-table-filter="search"]').value;
                        }
                    },
                    order: [[2, 'asc']],
                    pageLength: 10,
                    columns: [
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: id => `
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input row-checkbox" type="checkbox" value="${id}" />
                        </div>`
                        },
                        {
                            data: 'media_url',
                            orderable: false,
                            searchable: false,
                            render: data => `
                        <div class="banner-preview"
                             data-url="${data}"
                             style="width:80px;height:80px;cursor:pointer;">
                            <img src="${data}" style="width:100%;height:100%;object-fit:cover">
                        </div>`
                        },
                        {data: 'name'},
                        {data: 'line1'},
                        {data: 'line2'},
                        {data: 'line3'},
                        {data: 'created_at'},
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: id => `
                                            <div >
                                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-bs-toggle="dropdown"> Actions
                                                        <span class="svg-icon svg-icon-5 m-0">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/> </svg>
                                                            </span>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4">
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3 banner-delete" data-id="${id}"> Delete </a>
                                                        </div>
                                                        <div class="menu-item px-3">
                                                            <a href="{{ route('admin.speaker.add_edit_form') }}/${id}" class="menu-link px-3 "> Edit </a>
                                                        </div>
                                                    </div>
                                            </div>`
                        }
                    ]
                });

                datatable.on('draw', function () {
                    initToggleToolbar();
                    handleDeleteRows();
                    toggleToolbars();
                    KTMenu.createInstances();
                });
            };

            document.addEventListener('click', e => {

                const preview = e.target.closest('.banner-preview');
                if (preview) {
                    showBannerPreview(preview.dataset.url);
                }

                const del = e.target.closest('.banner-delete');
                if (del) {
                    e.preventDefault();
                    confirmDelete(del.dataset.id);
                }
            });

            function confirmDelete(id) {
                Swal.fire({
                    text: "Delete this speaker?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Delete"
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '{{ route("admin.speaker.delete", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        data: {_token: '{{ csrf_token() }}'},
                        success: function () {
                            toastr.success('Speaker has been deleted!');
                            datatable.draw(false);
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

            var handleDeleteRows = () => {
            };

            var toggleToolbars = function () {
                toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
                toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
                selectedCount = document.querySelector('[data-kt-user-table-select="selected_count"]');

                const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');
                let count = [...allCheckboxes].filter(c => c.checked).length;

                if (count > 0) {
                    selectedCount.innerHTML = count;
                    toolbarBase.classList.add('d-none');
                    toolbarSelected.classList.remove('d-none');
                } else {
                    toolbarBase.classList.remove('d-none');
                    toolbarSelected.classList.add('d-none');
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
                            text: "Please select at least one speaker.",
                            icon: "info"
                        });
                        return;
                    }

                    Swal.fire({
                        text: `Delete ${ids.length} selected speaker(s)?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete"
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        fetch('{{ route("admin.speaker.deleteMultiple") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ids})
                        })
                            .then(res => res.json())
                            .then(() => {
                                toastr.success('Selected speakers deleted successfully!');
                                datatable.draw(false);
                            });
                    });
                });

            return {
                init: function () {
                    if (!table) return;
                    initSpeakerTable();
                    initToggleToolbar();
                    handleSearchDatatable();
                }
            }
        }();

        KTUtil.onDOMContentLoaded(function () {
            KTSpeakersList.init();
        });

        function showBannerPreview(fileUrl) {
            const modal = new bootstrap.Modal(document.getElementById('bannerPreviewModal'));
            const modalTitle = document.getElementById('bannerPreviewTitle');
            const modalBody = document.getElementById('bannerPreviewBody');

            modalTitle.textContent = 'Speakers';
            modalBody.innerHTML = '';

            const img = document.createElement('img');
            img.src = fileUrl;
            img.className = 'img-fluid rounded';
            img.style.maxHeight = '70vh';
            modalBody.appendChild(img);

            modal.show();
        }
    </script>

@endpush
