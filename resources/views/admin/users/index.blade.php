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
                                <input type="text"
                                       data-kt-user-table-filter="search"
                                       class="form-control form-control-solid w-250px ps-14"
                                       placeholder="Search user"/>
                            </div>
                        </div>

                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
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

                                <a href="{{ route('admin.user.add_edit_form') }}" class="btn btn-primary">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1"
                                                  transform="rotate(-90 11.364 20.364)" fill="black"/>
                                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black"/>
                                        </svg>
                                    </span>
                                    Add User
                                </a>
                            </div>

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
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th></th>
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_users .row-checkbox"
                                               value="1"/>
                                    </div>
                                </th>
                                @if(auth()->user()->type === 'admin')
                                    <th>Event</th>
                                @endif
                                @foreach($valid_dynamic_fields as $field)
                                    <th>{{ $field->label }}</th>
                                @endforeach
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_view_user" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_view_user_header">
                    <h2 class="fw-bolder">User Details</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                      transform="rotate(-45 6 17.3137)" fill="black"/>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                      transform="rotate(45 7.41422 6)" fill="black"/>
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
@endsection

@push('scripts')

    <script>
        "use strict";
        const qsa = (s, p = document) => [...p.querySelectorAll(s)];
        const isAdmin = {{ auth()->user()->type === 'admin' ? 'true' : 'false' }};
        var KTUsersList = function () {
            var table = document.getElementById('kt_table_users');
            let userTable;

            const dynamicFields = @json(
                $valid_dynamic_fields
                    ->map(fn($field) => [
                        'field_name' => $field->field_name,
                        'label'      => $field->label,
                    ])
                    ->values()
                    ->toArray()
            );

            const editUrl = '{{ route("admin.user.add_edit_form", ":id") }}';
            const showUrl = '{{ route("admin.user.show", ":id") }}';
            const deleteUrl = '{{ route("admin.user.destroy", ":id") }}';

            function initUserTable() {
                userTable = $('#kt_table_users').DataTable({
                    processing: true,
                    serverSide: true,
                    searchDelay: 500,
                    ajax: {
                        url: '{{ route("admin.user.datatable") }}',
                        data: d => {
                            d.search = $('[data-kt-user-table-filter="search"]').val();
                        }
                    },
                    order: [[2, 'asc']],
                    columns: [
                        {data: null, orderable: false, defaultContent: ''},
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: id => `
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input row-checkbox" type="checkbox" value="${id}"/>
                                </div>`
                        },
                        ...(isAdmin ? [{data: 'event_name'}] : []),
                        ...dynamicFields.map(f => ({
                            data: f.field_name,
                            render: (data) => data ?? 'N/A'
                        })),
                        {
                            data: 'id',
                            orderable: false,
                            render: id => `
                                <div>
                                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-bs-toggle="dropdown"> Actions
                                                        <span class="svg-icon svg-icon-5 m-0">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/> </svg>
                                                            </span>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4">
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3 delete-user" data-id="${id}"> Delete </a>
                                                        </div>
                                                        <div class="menu-item px-3">
                                                            <a href="${showUrl.replace(':id', id)}" class="menu-link px-3 "> Show </a>
                                                        </div>
                                                        <div class="menu-item px-3">
                                                            <a href="${editUrl.replace(':id', id)}" class="menu-link px-3 "> Edit </a>
                                                        </div>
                                                    </div>
                                            </div>`
                        }
                    ]
                });
            }

            $('[data-kt-user-table-filter="search"]').on('keyup', function () {
                userTable.draw();
            });

            $(document).on('click', '.delete-user', function () {
                deleteUser($(this).data('id'));
            });

            function handleExport() {
                const exportBtn = document.getElementById('export-btn');
                if (!exportBtn) return;

                exportBtn.addEventListener('click', function () {
                    const originalHTML = exportBtn.innerHTML;
                    exportBtn.disabled = true;
                    exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';

                    const url = new URL('{{ route("admin.user.export") }}', window.location.origin);
                    const searchValue = $('[data-kt-user-table-filter="search"]').val();
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
                            link.setAttribute('download', 'users_export_' + new Date().toISOString().slice(0, 10) + '.csv');
                            link.style.visibility = 'hidden';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            exportBtn.disabled = false;
                            exportBtn.innerHTML = originalHTML;
                            toastr.success('Users exported successfully!');
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
                    const ids = qsa('.row-checkbox:checked').map(cb => cb.value);

                    if (!ids.length) {
                        Swal.fire({text: "Please select at least one user.", icon: "info", confirmButtonText: "OK"});
                        return;
                    }

                    Swal.fire({
                        text: `Delete ${ids.length} selected user(s)?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete",
                        cancelButtonText: "Cancel"
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        fetch('{{ route("admin.user.deleteMultiple") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ids})
                        })
                            .then(res => res.json().then(data => ({ok: res.ok, data})).catch(() => ({
                                ok: res.ok,
                                data: {}
                            })))
                            .then(({ok, data}) => {

                                if (ok) {
                                    toastr.success(data.message ?? "User Deleted successfully.");
                                } else {
                                    toastr.warning(data.message ?? "Some deletions failed.");
                                }

                                userTable.draw(false);
                            })
                            .catch(() => {

                                toastr.error("Failed to delete users.");

                            });
                    });
                });

            document.addEventListener('change', e => {
                if (!e.target.classList.contains('row-checkbox') &&
                    !e.target.matches('[data-kt-check="true"]')) return;

                const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;
                const toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
                const toolbarSel = document.querySelector('[data-kt-user-table-toolbar="selected"]');
                const selectedCountEl = document.querySelector('[data-kt-user-table-select="selected_count"]');

                if (selectedCount > 0) {
                    toolbarBase.classList.add('d-none');
                    toolbarSel.classList.remove('d-none');
                    selectedCountEl.textContent = selectedCount;
                } else {
                    toolbarBase.classList.remove('d-none');
                    toolbarSel.classList.add('d-none');
                    selectedCountEl.textContent = '';
                }
            });

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
                }).then(result => {

                    if (!result.isConfirmed) return;

                    fetch(deleteUrl.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {

                            userTable.draw();
                            toastr.success(data.message ?? 'User deleted successfully!');
                        })
                        .catch(() => {
                            toastr.error('Something went wrong!');
                        });

                });
            }

            return {
                init: function () {
                    if (!table) return;
                    initUserTable();
                    handleExport();
                }
            };
        }

        ();

        KTUtil.onDOMContentLoaded(function () {
            KTUsersList.init();
        });
    </script>
@endpush
