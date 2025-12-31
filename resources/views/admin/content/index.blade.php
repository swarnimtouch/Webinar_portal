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
                                       class="form-control form-control-solid w-250px ps-14" placeholder="Search Content"/>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-80px">ID</th>
                                <th class="min-w-125px">Title</th>
                                <th class="min-w-150px">Slug</th>
                                <th class="min-w-150px">Content</th>
                                <th class="min-w-150px">Created At</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                            @forelse($contents as $content)
                                <tr>
                                    <!-- ID -->
                                    <td>{{ $loop->iteration }}</td>

                                    <!-- Title -->
                                    <td>{{ $content->title ?? '-' }}</td>

                                    <!-- Slug -->
                                    <td>{{ $content->slug ?? '-' }}</td>

                                    <!-- Content -->
                                    <td>{{ $content->content ?? '-' }}</td>

                                    <!-- Created -->
                                    <td>{{ $content->created_at->format('d M Y') }}</td>
                                    <!-- Actions -->

{{--                                    <td class="text-end">--}}
{{--                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">--}}
{{--                                            Actions--}}
{{--                                            <span class="svg-icon svg-icon-5 m-0">--}}
{{--                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black"/>--}}
{{--                                                    </svg>--}}
{{--                                                </span>--}}
{{--                                        </a>--}}

{{--                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">--}}
{{--                                            <div class="menu-item px-3">--}}
{{--                                                --}}{{--                                                <a href="{{ route('user.show', $user->id) }}" class="menu-link px-3">View</a>--}}
{{--                                            </div>--}}
{{--                                            <div class="menu-item px-3">--}}
{{--                                                <a href="{{ route('speaker.create', $content->id) }}" class="menu-link px-3">Edit</a>--}}
{{--                                            </div>--}}
{{--                                            <div class="menu-item px-3">--}}
{{--                                                <a href="javascript:void(0)" onclick="deleteSpeaker({{ $speaker->id }})" class="menu-link px-3 text-danger">Delete</a>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
                                    <!-- Actions -->
                                    <td class="text-end">
                                        <a href="{{ route('content.edit', $content->id) }}"
                                           class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                           title="Edit">
                                            <span class="svg-icon svg-icon-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black"/>
                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black"/>
                                                </svg>
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">No content found</td>
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

            // Initialize datatable
            var initUserTable = function () {
                datatable = $(table).DataTable({
                    searchDelay: 500,
                    processing: true,
                    order: [[0, 'desc']], // Order by ID column
                    stateSave: false,
                    columnDefs: [
                        {
                            orderable: false,
                            targets: [5] // Disable ordering on actions column
                        }
                    ]
                });
            }

            // Search Datatable
            var handleSearchDatatable = function () {
                const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
                filterSearch.addEventListener('keyup', function (e) {
                    datatable.search(e.target.value).draw();
                });
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

        // On document ready
        KTUtil.onDOMContentLoaded(function () {
            KTUsersList.init();
        });
    </script>
@endpush
