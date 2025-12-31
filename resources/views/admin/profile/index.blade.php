@extends('layouts.master')

@section('title', 'Profile')

@section('body')
    @include('partials.header')


    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
    <div id="kt_content_container" class="container-xxl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <!--begin::Post-->
        <div class="card mb-5 mb-xl-10">
        <!--begin::Card header-->
        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Profile Details</h3>
            </div>
            <!--end::Card title-->
        </div>
        <!--begin::Card header-->
        <!--begin::Content-->
        <div id="kt_account_profile_details" class="collapse show" >

            <!--begin::Form-->
            <form id="kt_account_profile_details_form"
                  class="form"
                  method="POST"
                  action="{{ route('admin.profile.update') }}"
                  enctype="multipart/form-data">   {{-- 👈 avatar ke liye --}}

                @csrf

                <!--begin::Card body-->
                <div class="card-body border-top p-9">
                    <!--begin::Input group-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Avatar</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Image input-->
                            <div class="image-input image-input-outline" data-kt-image-input="true" >
                                <!--begin::Preview existing avatar-->
                                <div class="image-input-wrapper w-125px h-125px"
                                     style="background-image: url({{ $user->avatar
                                 ? asset('storage/'.$user->avatar)
                                 : asset('assets/media/avatars/blank.png') }})">
                                </div>

                                <!--end::Preview existing avatar-->
                                <!--begin::Label-->
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <!--begin::Inputs-->
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="avatar_remove" />
                                    <!--end::Inputs-->
                                </label>
                                <!--end::Label-->
                                <!--begin::Cancel-->
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
																<i class="bi bi-x fs-2"></i>
															</span>
                                <!--end::Cancel-->
                                <!--begin::Remove-->
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
																<i class="bi bi-x fs-2"></i>
															</span>
                                <!--end::Remove-->
                            </div>
                            <!--end::Image input-->
                            <!--begin::Hint-->
                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-bold fs-6">Name</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="name" value="{{ $user->name }}"  class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Please enter name" />
                                    <small class="error-msg text-danger d-none">
                                        Name is required
                                    </small>

                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->

                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-bold fs-6">User Name</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Row-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col-lg-12 fv-row">
                                    <input type="text" name="username" value="{{ $user->username }}"  class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Please enter username" />
                                    <small class="error-msg text-danger d-none">
                                        UserName is required
                                    </small>

                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->

                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row mb-6">
                        <!--begin::Label-->
                        <label class="col-lg-4 col-form-label required fw-bold fs-6">Email</label>
                        <!--end::Label-->
                        <!--begin::Col-->
                        <div class="col-lg-8 fv-row">
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control form-control-lg form-control-solid" placeholder="Email Id"  />

                            <small class="error-msg text-danger d-none">
                                Email is required
                            </small>

                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->


                </div>
                <!--end::Card body-->
                <!--begin::Actions-->
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset" class="btn btn-light btn-active-light-primary me-2"onclick="window.location='{{ route('dashboard') }}'">Discard</button>
                    <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">Save Changes</button>
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Content-->
    </div>
    </div>
    </div>
    @include('partials.footer')
@endsection

@push('scripts')
    <!--begin::Page Custom Javascript(used by this page)-->
    <script src="{{ asset('assets/js/custom/widgets.js')}}"></script>
    <script>
        const DEFAULT_AVATAR = "{{ asset('assets/media/avatars/blank.png') }}";
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Metronic image-input element
            const imageInput = document.querySelector('[data-kt-image-input="true"]');

            if (!imageInput) return;

            const wrapper = imageInput.querySelector('.image-input-wrapper');

            // 🔁 CANCEL click → restore default avatar
            imageInput.querySelector('[data-kt-image-input-action="cancel"]')
                .addEventListener('click', function () {
                    wrapper.style.backgroundImage = `url('${DEFAULT_AVATAR}')`;
                });

            // ❌ REMOVE click → also restore default avatar
            imageInput.querySelector('[data-kt-image-input-action="remove"]')
                .addEventListener('click', function () {
                    wrapper.style.backgroundImage = `url('${DEFAULT_AVATAR}')`;
                });

        });
    </script>

    <script>
        "use strict";

        var KTProfileUpdate = function () {
            var form;
            var submitButton;
            var validator;

            return {
                init: function () {

                    form = document.querySelector("#kt_account_profile_details_form");
                    submitButton = document.querySelector("#kt_account_profile_details_submit");

                    if (!form || !submitButton) return;

                    // 🔥 FormValidation init (same as password page)
                    validator = FormValidation.formValidation(form, {
                        fields: {
                            name: {
                                validators: {
                                    notEmpty: {
                                        message: "Name is required"
                                    }
                                }
                            },
                            username: {
                                validators: {
                                    notEmpty: {
                                        message: "UserName is required"
                                    }
                                }
                            },
                            email: {
                                validators: {
                                    notEmpty: {
                                        message: "Email is required"
                                    },
                                    emailAddress: {
                                        message: "The value is not a valid email address"
                                    }
                                }
                            }
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: '.fv-row',
                                eleInvalidClass: '',
                                eleValidClass: ''
                            })
                        }
                    });

                    // 🔐 Submit handler
                    submitButton.addEventListener("click", function (e) {
                        e.preventDefault();

                        validator.validate().then(function (status) {
                            if (status === 'Valid') {
                                submitButton.setAttribute("data-kt-indicator", "on");
                                submitButton.disabled = true;
                                form.submit();
                            }
                        });
                    });
                }
            };
        }();

        KTUtil.onDOMContentLoaded(function () {
            KTProfileUpdate.init();
        });
    </script>




@endpush
