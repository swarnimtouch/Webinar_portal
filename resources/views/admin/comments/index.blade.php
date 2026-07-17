@extends('layouts.admin')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="post d-flex flex-column-fluid">
        <div class="container-xxl">
            <div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title"><div class="d-flex align-items-center position-relative my-1">
            <i class="bi bi-search position-absolute ms-6"></i>
            <input type="text" id="commentsSearch" class="form-control form-control-solid w-250px ps-14" placeholder="Search comments">
        </div></div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-thread-table-toolbar="base">
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
                                        data-kt-thread-table-filter="event"
                                        data-hide-search="true">

                                    <option></option>
                                    @foreach($events ?? [] as $group)
                                        <option
                                            value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                                <button type="reset"
                                        class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                        data-kt-menu-dismiss="true"
                                        data-kt-thread-table-filter="reset">
                                    Reset
                                </button>
                                <button type="submit"
                                        class="btn btn-primary fw-bold px-6"
                                        data-kt-menu-dismiss="true"
                                        data-kt-thread-table-filter="filter">
                                    Apply
                                </button>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Filter Menu-->
                @endif
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

            <!--begin::Group actions-->
            <div class="d-flex justify-content-end align-items-center d-none"
                 data-kt-thread-table-toolbar="selected">
                <div class="fw-bolder me-5">
                    <span class="me-2" data-kt-thread-table-select="selected_count"></span>Selected
                </div>
                <button type="button" class="btn btn-danger"
                        data-kt-thread-table-select="delete_selected">
                    Delete Selected
                </button>
            </div>
            <!--end::Group actions-->
        </div>
    </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="commentsTable">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" id="commentsSelectAll">
                                        </div>
                                    </th>
                                    @if(auth()->user()->type === 'admin')
                                        <th>Event</th>
                                    @endif
                                    @foreach($fields as $field)
                                        <th>{{ $field->label }}</th>
                                    @endforeach
                                    <th>Comment</th>
                                    <th>Upvotes</th>
                                    <th>Publish</th>
                                    <th>Date</th>
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
</div>
@endsection

@push('scripts')
<script>
(() => {
    const isAdmin = @json(auth()->user()->type === 'admin');
    const eventFilter = $('[data-kt-thread-table-filter="event"]');
    const fields = @json($fields->map(fn($field) => ['name' => $field->field_name, 'label' => $field->label])->values());
    const textRenderer = $.fn.dataTable.render.text();
    const columns = [{data:'id', orderable:false, searchable:false, render:id => `<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input comment-row-check" type="checkbox" value="${id}"></div>`}];
    if (isAdmin) columns.push({data:'event', render:textRenderer.display});
    fields.forEach(field => columns.push({data:field.name, defaultContent:'N/A', render:textRenderer.display}));
    columns.push(
        {data:'comment', render:textRenderer.display},
        {data:'votes_count', searchable:false},
        {data:'is_approved', orderable:false, searchable:false, render:(approved,type,row) => `<div class="form-check form-switch form-check-custom form-check-solid"><input class="form-check-input comment-status-toggle" type="checkbox" aria-label="Publish comment" data-id="${row.id}" ${approved ? 'checked' : ''}></div>`},
        {data:'created_at'},
        {data:'id', orderable:false, searchable:false, render:id => `<div class="text-end"><a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-bs-toggle="dropdown">Actions <span class="svg-icon svg-icon-5 m-0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor"/></svg></span></a><div class="dropdown-menu dropdown-menu-end menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"><div class="menu-item px-3"><a href="#" class="menu-link px-3 comment-delete" data-id="${id}">Delete</a></div></div></div>`}
    );

    const table = $('#commentsTable').DataTable({
        processing:true, serverSide:true, searchDelay:400, pageLength:10,
        ajax:{url:@json(route('admin.comments.datatable')), data:d => { d.search = $('#commentsSearch').val(); if(isAdmin) d.event = eventFilter.val(); }},
        columns, order:[[columns.length - 2, 'desc']],
    });

    let timer; $('#commentsSearch').on('input', function(){ clearTimeout(timer); timer=setTimeout(()=>table.draw(),400); });
    $('[data-kt-thread-table-filter="filter"]').on('click', ()=>table.draw());
    $('[data-kt-thread-table-filter="reset"]').on('click', ()=>{ eventFilter.val(null).trigger('change'); table.draw(); });
    $('#export-btn').on('click', function () {
        const button = $(this);
        const originalHtml = button.html();
        const url = new URL(@json(route('admin.comments.export')), location.origin);

        url.searchParams.set('search', $('#commentsSearch').val());
        if (isAdmin) url.searchParams.set('event', eventFilter.val() || '');

        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Exporting...');

        fetch(url.toString(), {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(response => {
                if (!response.ok) throw new Error('Export failed');
                return response.blob();
            })
            .then(blob => {
                const downloadUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = `comments_export_${new Date().toISOString().slice(0, 10)}.csv`;
                document.body.appendChild(link);
                link.click();
                link.remove();
                URL.revokeObjectURL(downloadUrl);
                toastr.success('Comments exported successfully!');
            })
            .catch(() => toastr.error('Export failed. Please try again.'))
            .finally(() => button.prop('disabled', false).html(originalHtml));
    });

    const updateSelected = () => {
        const rowCheckboxes = $('.comment-row-check');
        const count = rowCheckboxes.filter(':checked').length;
        const hasSelection = count > 0;
        const baseToolbar = $('[data-kt-thread-table-toolbar="base"]');
        const selectedToolbar = $('[data-kt-thread-table-toolbar="selected"]');

        $('[data-kt-thread-table-select="selected_count"]').text(hasSelection ? count : '');
        baseToolbar.toggleClass('d-none', hasSelection);
        selectedToolbar.toggleClass('d-none', !hasSelection);
        baseToolbar.find('[data-kt-menu-trigger="click"]').prop('disabled', hasSelection);
        $('#commentsSelectAll').prop('checked', rowCheckboxes.length > 0 && count === rowCheckboxes.length);
    };
    $('#commentsSelectAll').on('change',function(){ $('.comment-row-check').prop('checked',this.checked); updateSelected(); });
    $('#commentsTable').on('change','.comment-row-check',updateSelected).on('draw.dt',()=>{ $('#commentsSelectAll').prop('checked',false); updateSelected(); });

    const remove = ids => fetch(@json(route('admin.comments.deleteMultiple')), {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@json(csrf_token())},body:JSON.stringify({ids})})
        .then(async response => { const data=await response.json(); if(!response.ok) throw new Error(data.message||'Delete failed'); return data; })
        .then(res=>{toastr.success(res.message);table.draw(false);}).catch(error=>toastr.error(error.message));
    const confirmDelete = (ids, multiple=false) => Swal.fire({
        text: multiple ? `Delete ${ids.length} selected comment(s)?` : 'Are you sure you want to delete this comment?',
        icon: 'warning', showCancelButton: true,
        confirmButtonText: multiple ? 'Yes, delete' : 'Yes, delete it!', cancelButtonText: 'Cancel'
    }).then(result => { if(result.isConfirmed) remove(ids); });
    $('[data-kt-thread-table-select="delete_selected"]').on('click',()=>{ const ids=$('.comment-row-check:checked').map((_,el)=>el.value).get(); if(ids.length) confirmDelete(ids,true); });
    $('#commentsTable').on('click','.comment-delete',function(){ confirmDelete([this.dataset.id]); });
    $('#commentsTable').on('change', '.comment-status-toggle', function () {
        const toggle = $(this);
        const enabled = this.checked;
        fetch(@json(route('admin.comments.toggleStatus', ['comment' => '__ID__'])).replace('__ID__', this.dataset.id), {
            method: 'POST',
            headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@json(csrf_token())},
            body: JSON.stringify({is_approved: enabled ? 1 : 0})
        }).then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Status update failed');
            toastr.success(data.message);
            table.draw(false);
        }).catch(error => {
            toggle.prop('checked', !enabled);
            toastr.error(error.message);
        });
    });
})();
</script>
@endpush
