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
                                       placeholder="Search by user or certificate..."/>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Filter-->

                                <!--begin::Menu-->
                                @if(auth()->user()->type === 'admin')

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
                                    <!--begin::Filter Menu-->
                                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                        <!--begin::Header-->
                                        <div class="px-7 py-5">
                                            <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                        </div>
                                        <!--end::Header-->
                                        <div class="separator border-gray-200"></div>
                                        <!--begin::Content-->
                                        <div class="px-7 py-5" data-kt-chat-message-table-filter="form">
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <label class="form-label fs-6 fw-bold">Event:</label>
                                                <select class="form-select form-select-solid fw-bolder"
                                                        data-kt-select2="true"
                                                        data-placeholder="Select Option"
                                                        data-allow-clear="true"
                                                        data-kt-user-table-filter="event"
                                                        data-hide-search="true">

                                                    <option></option>
                                                    @foreach($download ?? [] as $downloads)
                                                        <option
                                                            value="{{ $downloads->id }}">{{ $downloads->name }}</option>
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
                                                <button type="submit"
                                                        class="btn btn-primary fw-bold px-6"
                                                        data-kt-menu-dismiss="true"
                                                        data-kt-user-table-filter="filter">
                                                    Apply
                                                </button>
                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Filter Menu-->
                                @endif
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

                                <!--begin::Add user-->

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
                    </div>
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5"
                               id="kt_table_certificate_downloads">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_certificate_downloads .row-checkbox"
                                               value="1"/>
                                    </div>
                                </th>
                                @if(auth()->user()->type === 'admin')
                                    <th>Event</th>
                                @endif
                                <th>User</th>
                                <th>Certificate</th>
                                <th>File</th>
                                <th>Downloaded At</th>
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

        let downloadTable;
        const isAdmin = {{ auth()->user()->type === 'admin' ? 'true' : 'false' }};

        function initDownloadTable() {
            downloadTable = $('#kt_table_certificate_downloads').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '{{ route("admin.certificate_log.datatable") }}',
                    data: d => {
                        d.search = document.querySelector('[data-kt-user-table-filter="search"]').value;
                        if (isAdmin) {
                            const eventEl = document.querySelector('[data-kt-user-table-filter="event"]');
                            if (eventEl) d.event = eventEl.value;
                        }
                    }
                },
                order: [[3, 'desc']],
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        render: id => `
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input row-checkbox" type="checkbox" value="${id}">
                        </div>`
                    },
                    ...(isAdmin ? [{data: 'event'}] : []),
                    {
                        data: 'user_name',
                        render: (name, type, row) => `
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-800">${name}</span>
                            <small class="text-muted">${row.user_email}</small>
                        </div>`
                    },
                    {
                        data: 'certificate_name',
                        render: name => `<span class="badge badge-light-primary">${name}</span>`
                    },
                    {
                        data: 'file_path',
                        orderable: false,
                        render: (path, type, row) => {
                            if (!path) return `<span class="badge badge-light-warning">No File</span>`;
                            return `<a href="${row.file_url}" target="_blank" class="btn btn-sm btn-light-success">
                            <i class="bi bi-download me-1"></i>Download
                        </a>`;
                        }
                    },
                    {data: 'downloaded_at'},
                    {
                        data: 'id',
                        orderable: false,
                        render: id => `
                        <div>
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
                                    <a href="#" class="menu-link px-3 download-delete" data-id="${id}">Delete</a>
                                </div>
                            </div>
                        </div>`
                    }
                ]
            });
        }

        document.addEventListener('change', e => {
            if (!e.target.matches('[data-kt-check="true"]')) return;
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
            toggleBulkToolbar();
        });

        document.addEventListener('change', e => {
            if (!e.target.classList.contains('row-checkbox')) return;
            toggleBulkToolbar();
        });

        function toggleBulkToolbar() {
            const selected = document.querySelectorAll('.row-checkbox:checked').length;
            const baseToolbar = document.querySelector('[data-kt-user-table-toolbar="base"]');
            const selectedToolbar = document.querySelector('[data-kt-user-table-toolbar="selected"]');
            const countEl = document.querySelector('[data-kt-user-table-select="selected_count"]');

            if (selected > 0) {
                baseToolbar.classList.add('d-none');
                selectedToolbar.classList.remove('d-none');
                countEl.textContent = selected;
            } else {
                baseToolbar.classList.remove('d-none');
                selectedToolbar.classList.add('d-none');
                countEl.textContent = '';
            }
        }

        document.querySelector('[data-kt-user-table-select="delete_selected"]')
            ?.addEventListener('click', () => {
                const ids = [...document.querySelectorAll('.row-checkbox:checked')].map(cb => cb.value);

                if (!ids.length) {
                    Swal.fire({text: "Please select at least one record", icon: "info", confirmButtonText: "OK"});
                    return;
                }

                Swal.fire({
                    text: `Delete ${ids.length} selected record(s)?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete"
                }).then(result => {
                    if (!result.isConfirmed) return;

                    fetch('{{ route("admin.certificate_log.deleteMultiple") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ids})
                    })
                        .then(res => {
                            if (!res.ok) throw new Error();
                            return res.json();
                        })
                        .then(data => {
                            toastr.success(data.message || "Selected records deleted successfully");
                            downloadTable.draw(false);
                            toggleBulkToolbar();
                        })
                        .catch(() => toastr.error("Failed to delete records"));
                });
            });

        document.querySelector('[data-kt-user-table-filter="filter"]')
            ?.addEventListener('click', () => downloadTable.draw());

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

                downloadTable.draw();
            });

        document.querySelector('[data-kt-user-table-filter="search"]')
            .addEventListener('keyup', () => downloadTable.draw());

        document.addEventListener('click', e => {
            if (!e.target.classList.contains('download-delete')) return;

            e.preventDefault();
            const id = e.target.dataset.id;

            Swal.fire({
                text: "Delete this download record?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete"
            }).then(r => {
                if (!r.isConfirmed) return;

                $.ajax({
                    url: '{{ route("admin.certificate_log.delete", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: data => {
                        toastr.success(data.message);
                        downloadTable.draw(false);
                    },
                    error: xhr => toastr.error(xhr.responseJSON?.message ?? 'Delete failed.')
                });
            });
        });

        var handleExport = function () {
            const exportBtn = document.getElementById('export-btn');
            if (!exportBtn) return;

            exportBtn.addEventListener('click', function () {
                const originalHTML = exportBtn.innerHTML;
                exportBtn.disabled = true;
                exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';

                const url = new URL('{{ route("admin.certificate_log.export") }}', window.location.origin);
                const searchValue = document.querySelector('[data-kt-user-table-filter="search"]').value;
                if (searchValue) url.searchParams.set('search', searchValue);

                if (isAdmin) {
                    const eventEl = document.querySelector('[data-kt-user-table-filter="event"]');
                    if (eventEl && eventEl.value) url.searchParams.set('event', eventEl.value);
                }

                fetch(url.toString(), {method: 'GET', headers: {'X-Requested-With': 'XMLHttpRequest'}})
                    .then(res => {
                        if (!res.ok) throw new Error();
                        return res.blob();
                    })
                    .then(blob => {
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.setAttribute('download', 'certificate_downloads_export_' + new Date().toISOString().slice(0, 10) + '.csv');
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        exportBtn.disabled = false;
                        exportBtn.innerHTML = originalHTML;
                        toastr.success('Certificate downloads exported successfully!');
                    })
                    .catch(() => {
                        exportBtn.disabled = false;
                        exportBtn.innerHTML = originalHTML;
                        toastr.error('Export failed. Please try again.');
                    });
            });
        };

        handleExport();

        KTUtil.onDOMContentLoaded(() => initDownloadTable());
    </script>
@endpush
