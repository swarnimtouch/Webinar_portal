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
                                       placeholder="Search by user or certificate..."/>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Filter-->

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
                                <th>User</th>
                                <th>Certificate</th>
                                <th>File</th>
                                <th>Downloaded At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                            <!-- Data will be loaded via AJAX -->
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

        function initDownloadTable() {
            downloadTable = $('#kt_table_certificate_downloads').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '{{ route("admin.certificate-log.datatable") }}',
                    data: d => {
                        d.search = document.querySelector('[data-kt-user-table-filter="search"]').value;
                    }
                },
                order: [[3, 'desc']],
                columns: [

                    /* CHECKBOX */
                    {
                        data: 'id',
                        orderable: false,
                        render: id => `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input row-checkbox" type="checkbox" value="${id}">
                            </div>`
                    },

                    /* USER */
                    {
                        data: 'user_name',
                        render: (name, type, row) => `
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-gray-800">${name}</span>
                                <small class="text-muted">${row.user_email}</small>
                            </div>`
                    },

                    /* CERTIFICATE */
                    {
                        data: 'certificate_name',
                        render: name => `<span class="badge badge-light-primary">${name}</span>`
                    },
                    {
                        data: 'file_path',
                        orderable: false,
                        render: function (path) {

                            if (!path) {
                                return `<span class="badge badge-light-warning">No File</span>`;
                            }

                            let fullUrl = `/storage/${path}`;

                            return `
                            <a href="${fullUrl}" target="_blank"
                               class="btn btn-sm btn-light-success">
                                <i class="bi bi-download me-1"></i>Download
                            </a>`;
                        }
                    },
                    /* DOWNLOADED AT */
                    {data: 'downloaded_at'},

                    /* ACTIONS */
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

        /* ===== SELECT ALL CHECKBOX ===== */
        document.addEventListener('change', e => {
            if (!e.target.matches('[data-kt-check="true"]')) return;
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
            toggleBulkToolbar();
        });

        /* ===== SINGLE ROW CHECK ===== */
        document.addEventListener('change', e => {
            if (!e.target.classList.contains('row-checkbox')) return;
            toggleBulkToolbar();
        });

        /* ===== SHOW / HIDE BULK TOOLBAR ===== */
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

        /* ===== MULTIPLE DELETE ===== */
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

                    fetch('{{ route("admin.certificate-log.deleteMultiple") }}', {
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
                        .then(() => {
                            Swal.fire({text: "Selected records deleted successfully", icon: "success"});
                            downloadTable.draw(false);
                            toggleBulkToolbar();
                        })
                        .catch(() => {
                            Swal.fire({text: "Failed to delete records", icon: "error"});
                        });
                });
            });

        var handleExport = function () {
            const exportBtn = document.getElementById('export-btn');

            if (exportBtn) {
                exportBtn.addEventListener('click', function () {
                    const originalHTML = exportBtn.innerHTML;
                    exportBtn.disabled = true;
                    exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';

                    let csv = 'User,Email,Certificate,File,Downloaded At\n';

                    const rows = document.querySelectorAll('#kt_table_certificate_downloads tbody tr');
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length > 1) {
                            let rowData = [];

                            // col 0 = checkbox (skip)
                            // col 1 = User (name + email in two lines)
                            const userLines = cells[1].innerText.trim().split('\n');
                            rowData.push(`"${(userLines[0] || '').replace(/"/g, '""')}"`); // name
                            rowData.push(`"${(userLines[1] || '').replace(/"/g, '""')}"`); // email

                            // col 2 = Certificate
                            rowData.push(`"${cells[2].innerText.trim().replace(/"/g, '""')}"`);

                            // col 3 = File — href if link exists, else "No File"
                            const fileLink = cells[3].querySelector('a');
                            rowData.push(`"${fileLink ? fileLink.href : 'No File'}"`);

                            // col 4 = Downloaded At
                            rowData.push(`"${cells[4].innerText.trim().replace(/"/g, '""')}"`);

                            // col 5 = Actions (skip)

                            csv += rowData.join(',') + '\n';
                        }
                    });

                    const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'certificate_downloads_export_' + new Date().toISOString().slice(0, 10) + '.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    setTimeout(() => {
                        exportBtn.disabled = false;
                        exportBtn.innerHTML = originalHTML;

                        Swal.fire({
                            text: "Certificate downloads exported successfully!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {confirmButton: "btn fw-bold btn-primary"}
                        });
                    }, 500);
                });
            }
        };


        handleExport();
        /* ===== DELETE ===== */
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
                    url: '{{ route("admin.certificate-log.delete", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: () => {
                        toastr.success('Record deleted');
                        downloadTable.draw(false);
                    },
                    error: () => toastr.error('Failed to delete record')
                });
            });
        });

        /* ===== SEARCH ===== */
        document.querySelector('[data-kt-user-table-filter="search"]')
            .addEventListener('keyup', () => downloadTable.draw());

        KTUtil.onDOMContentLoaded(() => initDownloadTable());
    </script>
@endpush
