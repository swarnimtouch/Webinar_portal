@extends('layouts.admin')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->


        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
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
                                       placeholder="Search banner"/>
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
                                            <label class="form-label fs-6 fw-bold">Type:</label>
                                            <select class="form-select form-select-solid fw-bolder"
                                                    data-kt-select2="true"
                                                    data-placeholder="Select option" data-allow-clear="true"
                                                    data-kt-user-table-filter="type" data-hide-search="true">
                                                <option></option>
                                                <option value="image">Image</option>
                                                <option value="video">Video</option>
                                            </select>
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="mb-10">
                                            <label class="form-label fs-6 fw-bold">Status:</label>
                                            <select class="form-select form-select-solid fw-bolder"
                                                    data-kt-select2="true"
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
                                <a href="{{ route('admin.banner.add_edit_form') }}" class="btn btn-primary">
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
                                    Add Banner
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
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_banners">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class=""></th>
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_banners .row-checkbox"
                                               value="1"/>

                                    </div>
                                </th>
                                <th>Banner</th>
                                <th>Title</th>
                                <th>Event</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                            <!-- Data will be loaded via AJAX -->
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
        <div class="modal fade" id="videoPreviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-black">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 text-center">
                        <video id="modalVideoPlayer" controls autoplay
                               style="width:100%;max-height:70vh;background:#000;">
                        </video>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Post-->
        @endsection

        @push('scripts')
            <script>
                "use strict";
                const qs = (s, p = document) => p.querySelector(s);
                const qsa = (s, p = document) => [...p.querySelectorAll(s)];
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;


                let bannerTable;

                function initBannerTable() {
                    bannerTable = $('#kt_table_banners').DataTable({
                        processing: true,
                        serverSide: true,
                        searchDelay: 500,
                        ajax: {
                            url: '{{ route("admin.banner.datatable") }}',
                            data: d => {
                                d.search = qs('[data-kt-user-table-filter="search"]').value;
                                d.type = qs('[data-kt-user-table-filter="type"]').value;
                                d.status = qs('[data-kt-user-table-filter="status"]').value;
                            }
                        },
                        order: [[2, 'asc']],
                        pageLength: 10,
                        columns: [
                            {data: null, orderable: false, defaultContent: ''},
                            {
                                data: 'id',
                                orderable: false,
                                searchable: false,
                                render: id => `<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input row-checkbox" type="checkbox" value="${id}" />
                </div>`
                            },
                            {
                                data: 'media_url',
                                render: (data, type, row) =>
                                    row.type === 'image'
                                        ? `<a href="${row.media_url}" class="image-link">
                               <img src="${row.media_url}"
                                    style="width:80px;height:80px;object-fit:cover;border-radius:4px;">
                           </a>`
                                        : `<a href="#" class="video-link" data-url="${row.media_url}">
                               <div style="width:80px;height:80px;border-radius:4px;overflow:hidden;position:relative;background:#000;">
                                   <video muted style="width:100%;height:100%;object-fit:cover;">
                                       <source src="${row.media_url}" type="video/mp4">
                                   </video>
                                   <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                                       <svg width="28" height="28" viewBox="0 0 24 24" fill="white" opacity="0.85">
                                           <path d="M8 5v14l11-7z"/>
                                       </svg>
                                   </div>
                               </div>
                           </a>`
                            },
                            {data: 'title'},
                            {data: 'event'},
                            {data: 'created_at'},
                            {
                                data: 'status',
                                render: (data, type, row) => `
                    <div class="form-check form-switch">
                        <input class="form-check-input banner-status-toggle"
                               type="checkbox"
                               data-id="${row.id}"
                               ${row.status === 'active' ? 'checked' : ''}>
                    </div>`
                            },
                            {
                                data: 'id',
                                orderable: false,
                                searchable: false,
                                render: id => `<div>
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
                            <a href="#" class="menu-link px-3 banner-delete" data-id="${id}">Delete</a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="{{ route('admin.banner.add_edit_form') }}/${id}" class="menu-link px-3">Edit</a>
                        </div>
                    </div>
                </div>`
                            }
                        ]
                    });
                }

                document.addEventListener('click', e => {

                    const videoLink = e.target.closest('.video-link');
                    if (videoLink) {
                        e.preventDefault();
                        const player = document.getElementById('modalVideoPlayer');
                        player.src = videoLink.dataset.url;
                        player.load();

                        const modalEl = document.getElementById('videoPreviewModal');
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();

                        modalEl.addEventListener('shown.bs.modal', () => {
                            player.play().catch(() => {
                            });
                        }, {once: true});

                        modalEl.addEventListener('hidden.bs.modal', () => {
                            player.pause();
                            player.currentTime = 0;
                            player.src = '';
                        }, {once: true});
                        return;
                    }

                    const del = e.target.closest('.banner-delete');
                    if (del) {
                        e.preventDefault();
                        confirmDelete(del.dataset.id);
                    }
                });

                document.addEventListener('mouseenter', e => {
                    if (e.target.tagName === 'VIDEO' && e.target.id !== 'modalVideoPlayer')
                        e.target.play().catch(() => {
                        });
                }, true);

                document.addEventListener('mouseleave', e => {
                    if (e.target.tagName === 'VIDEO' && e.target.id !== 'modalVideoPlayer') {
                        e.target.pause();
                        e.target.currentTime = 0;
                    }
                }, true);

                document.addEventListener('change', e => {
                    if (!e.target.classList.contains('banner-status-toggle')) return;

                    const checkbox = e.target;
                    const id = checkbox.dataset.id;
                    const status = checkbox.checked ? 'active' : 'inactive';

                    Swal.fire({
                        text: "Change banner status?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes"
                    }).then(result => {
                        if (!result.isConfirmed) {
                            checkbox.checked = !checkbox.checked;
                            return;
                        }

                        $.ajax({
                            url: '{{ route("admin.banner.toggleStatus", ":id") }}'.replace(':id', id),
                            method: 'POST',
                            data: {_token: '{{ csrf_token() }}', status},
                            success: data => toastr.success(data.message),
                            error: xhr => {
                                checkbox.checked = !checkbox.checked;
                                toastr.error(xhr.responseJSON?.message ?? 'Error updating status.');
                            }
                        });
                    });
                });

                function confirmDelete(id) {
                    Swal.fire({
                        text: "Delete this banner?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Delete"
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            url: '{{ route("admin.banner.delete", ":id") }}'.replace(':id', id),
                            method: 'DELETE',
                            data: {_token: '{{ csrf_token() }}'},
                            success: data => {
                                toastr.success(data.message);
                                bannerTable.draw(false);
                            },
                            error: xhr => toastr.error(xhr.responseJSON?.message ?? 'Delete failed.')
                        });
                    });
                }

                document.querySelector('[data-kt-user-table-select="delete_selected"]')
                    ?.addEventListener('click', () => {
                        const ids = qsa('.row-checkbox:checked').map(cb => cb.value);

                        if (!ids.length) {
                            Swal.fire({text: "Please select at least one banner.", icon: "info"});
                            return;
                        }

                        Swal.fire({
                            text: `Delete ${ids.length} selected banner(s)?`,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Yes, delete"
                        }).then(result => {
                            if (!result.isConfirmed) return;

                            fetch('{{ route("admin.banner.deleteMultiple") }}', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf},
                                body: JSON.stringify({ids})
                            })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.message) toastr.success(data.message);
                                    bannerTable.draw(false);
                                })
                                .catch(() => toastr.error('Delete failed.'));
                        });
                    });

                qs('[data-kt-user-table-filter="search"]').addEventListener('keyup', () => bannerTable.draw());
                qs('[data-kt-user-table-filter="filter"]').addEventListener('click', () => bannerTable.draw());
                qs('[data-kt-user-table-filter="reset"]').addEventListener('click', () => {
                    qs('[data-kt-user-table-filter="type"]').value = '';
                    qs('[data-kt-user-table-filter="status"]').value = '';
                    bannerTable.draw();
                });

                document.addEventListener('change', e => {
                    if (!e.target.matches('[data-kt-check="true"]')) return;
                    qsa('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
                });

                document.addEventListener('change', e => {
                    if (!e.target.classList.contains('row-checkbox') &&
                        !e.target.matches('[data-kt-check="true"]')) return;

                    const count = document.querySelectorAll('.row-checkbox:checked').length;
                    const toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
                    const toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
                    const countEl = document.querySelector('[data-kt-user-table-select="selected_count"]');

                    if (count > 0) {
                        toolbarBase.classList.add('d-none');
                        toolbarSelected.classList.remove('d-none');
                        countEl.textContent = count;
                    } else {
                        toolbarBase.classList.remove('d-none');
                        toolbarSelected.classList.add('d-none');
                        countEl.textContent = '';
                    }

                    document.querySelectorAll('.banner-status-toggle')
                        .forEach(t => t.disabled = count > 1);
                });

                KTUtil.onDOMContentLoaded(() => {
                    initBannerTable();
                    $('#kt_table_banners').on('draw.dt', () => $('.image-link').viewbox());
                });

            </script>

    @endpush
