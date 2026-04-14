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
                                <input type="text"
                                       data-kt-chat-message-table-filter="search"
                                       class="form-control form-control-solid w-250px ps-14"
                                       placeholder="Search messages..."/>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--end::Card title-->

                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-chat-message-table-toolbar="base">

                                <!--begin::Filter-->
                                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
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
                                            <label class="form-label fs-6 fw-bold">Group:</label>
                                            <select class="form-select form-select-solid fw-bolder"
                                                    data-kt-select2="true"
                                                    data-placeholder="Select group"
                                                    data-allow-clear="true"
                                                    data-kt-chat-message-table-filter="group"
                                                    data-hide-search="true">
                                                <option></option>
                                                @foreach($groups ?? [] as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="d-flex justify-content-end">
                                            <button type="reset"
                                                    class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-chat-message-table-filter="reset">
                                                Reset
                                            </button>
                                            <button type="submit"
                                                    class="btn btn-primary fw-bold px-6"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-chat-message-table-filter="filter">
                                                Apply
                                            </button>
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Filter Menu-->

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
                            </div>
                            <!--end::Toolbar-->

                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none"
                                 data-kt-chat-message-table-toolbar="selected">
                                <div class="fw-bolder me-5">
                                    <span class="me-2" data-kt-chat-message-table-select="selected_count"></span>
                                    Selected
                                </div>
                                <button type="button" class="btn btn-danger"
                                        data-kt-chat-message-table-select="delete_selected">
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
                        <table class="table align-middle table-row-dashed fs-6 gy-5"
                               id="kt_table_chat_messages">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               data-kt-check="true"
                                               data-kt-check-target="#kt_table_chat_messages .row-checkbox"
                                               value="1"/>
                                    </div>
                                </th>
                                <th>Group</th>
                                <th>Sender</th>
                                <th>Message</th>
                                <th>Seen By</th>
                                <th>Date</th>
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
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        "use strict";

        let chatMessageTable;

        function initChatMessageTable() {
            chatMessageTable = $('#kt_table_chat_messages').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '{{ route("admin.chatlog.datatable") }}',
                    data: d => {
                        d.search = document.querySelector('[data-kt-chat-message-table-filter="search"]').value;
                        d.group = document.querySelector('[data-kt-chat-message-table-filter="group"]')?.value ?? '';
                    }
                },
                order: [[5, 'desc']],
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

                    /* GROUP */
                    {
                        data: 'group_name',
                        render: name => `<span class="fw-bold text-gray-800">${name}</span>`
                    },

                    /* SENDER */
                    {data: 'sender_name'},

                    /* MESSAGE */
                    {
                        data: 'message',
                        render: data => data.length > 60 ? data.substr(0, 60) + '...' : data
                    },

                    /* SEEN BY */
                    {data: 'seen_by'},

                    /* DATE */
                    {data: 'created_at'},

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
                                <a href="#" class="menu-link px-3 message-delete" data-id="${id}">Delete</a>
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

            const checked = e.target.checked;
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = checked;
            });

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

            const baseToolbar = document.querySelector('[data-kt-chat-message-table-toolbar="base"]');
            const selectedToolbar = document.querySelector('[data-kt-chat-message-table-toolbar="selected"]');
            const countEl = document.querySelector('[data-kt-chat-message-table-select="selected_count"]');

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
        document.querySelector('[data-kt-chat-message-table-select="delete_selected"]')
            ?.addEventListener('click', () => {

                const ids = [...document.querySelectorAll('.row-checkbox:checked')]
                    .map(cb => cb.value);

                if (!ids.length) {
                    Swal.fire({
                        text: "Please select at least one message",
                        icon: "info",
                        confirmButtonText: "OK"
                    });
                    return;
                }

                Swal.fire({
                    text: `Delete ${ids.length} selected message(s)?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete"
                }).then(result => {
                    if (!result.isConfirmed) return;

                    fetch('{{ route("admin.chatlog.deleteMultiple") }}', {
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
                            Swal.fire({
                                text: "Selected messages deleted successfully",
                                icon: "success"
                            });
                            chatMessageTable.draw(false);
                            toggleBulkToolbar();
                        })
                        .catch(() => {
                            Swal.fire({
                                text: "Failed to delete messages",
                                icon: "error"
                            });
                        });
                });
            });

        /* ===== SINGLE DELETE ===== */
        document.addEventListener('click', function (e) {

            if (!e.target.classList.contains('message-delete')) return;

            e.preventDefault();
            const id = e.target.dataset.id;

            Swal.fire({
                text: "Delete this message?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete"
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route("admin.chatlog.delete", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {

                        Swal.fire({
                            text: "Message deleted successfully",
                            icon: "success"
                        });

                        chatMessageTable.draw(false);
                        toggleBulkToolbar();
                    },
                    error: function () {

                        Swal.fire({
                            text: "Failed to delete message",
                            icon: "error"
                        });
                    }
                });

            });
        });

        /* ===== EXPORT ===== */
        var handleExport = function () {
            const exportBtn = document.getElementById('export-btn');

            if (exportBtn) {
                exportBtn.addEventListener('click', function () {
                    const originalHTML = exportBtn.innerHTML;
                    exportBtn.disabled = true;
                    exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';

                    // CSV headers: Group, Sender, Message, Seen By, Date
                    let csv = 'Group,Sender,Message,Seen By,Date\n';

                    const rows = document.querySelectorAll('#kt_table_chat_messages tbody tr');
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length > 1) {
                            let rowData = [];
                            // Skip checkbox column (index 0) and actions column (last)
                            for (let i = 1; i < cells.length - 1; i++) {
                                let text = cells[i].innerText.trim().replace(/\n/g, ' ');
                                rowData.push(`"${text}"`);
                            }
                            csv += rowData.join(',') + '\n';
                        }
                    });

                    const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'chat_messages_export_' + new Date().toISOString().slice(0, 10) + '.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    setTimeout(() => {
                        exportBtn.disabled = false;
                        exportBtn.innerHTML = originalHTML;

                        Swal.fire({
                            text: "Messages exported successfully!",
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
        };

        handleExport();


        /* ===== SEARCH ===== */
        document.querySelector('[data-kt-chat-message-table-filter="search"]')
            .addEventListener('keyup', () => chatMessageTable.draw());

        /* ===== FILTER (Apply button) ===== */
        document.querySelector('[data-kt-chat-message-table-filter="filter"]')
            ?.addEventListener('click', () => chatMessageTable.draw());

        /* ===== FILTER (Reset button) ===== */
        document.querySelector('[data-kt-chat-message-table-filter="reset"]')
            ?.addEventListener('click', () => {
                document.querySelector('[data-kt-chat-message-table-filter="group"]').value = '';
                chatMessageTable.draw();
            });

        KTUtil.onDOMContentLoaded(() => initChatMessageTable());
    </script>
@endpush
