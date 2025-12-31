@extends('layouts.master')

@section($title,'title')

@section('body')
    @include('partials.header')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Toolbar-->

    <div class="post d-flex flex-column-fluid" id="kt_post">
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

            <!-- Create/Edit Speaker Card -->
            <div class="card mb-5 mb-xl-10">
                <div id="kt_speaker_wrapper" class="collapse show">
                    <form method="POST"
                          action="{{ route('speakers.store', isset($speaker) ? $speaker->id : null) }}"
                          id="kt_speaker_form"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="type" id="type" value="">
                        <input type="hidden" name="status" value="{{ isset($speaker) ? $speaker->status : 'active' }}">
                        <input type="hidden" name="image_removed" id="image_removed" value="0">
                        <input type="hidden" name="has_existing_image" id="has_existing_image" value="{{ isset($speaker) && $speaker->filename ? '1' : '0' }}">

                        <div class="card-body border-top p-9">
                            <div class="row mb-6" id="imageUploadSection">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Image</label>
                                <div class="col-lg-8">
                                    <!--begin::Image input-->
                                    <div class="image-input image-input-outline" data-kt-image-input="true"
                                         style="background-image: url('{{ asset('assets/media/avatars/blank.png') }}')">

                                        <!--begin::Preview existing image-->
                                        <div class="image-input-wrapper w-125px h-125px" id="imagePreview"
                                             style="background-image: url('{{ isset($speaker) && $speaker->filename ? asset('storage/speakers/' . $speaker->filename) : asset('assets/media/avatars/blank.png') }}')">
                                        </div>
                                        <!--end::Preview existing image-->

                                        <!--begin::Label-->
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                               data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <!--begin::Inputs-->
                                            <input type="file" name="filename" id="filename" accept="image/*" />
                                            <input type="hidden" name="image_remove" />
                                            <!--end::Inputs-->
                                        </label>
                                        <!--end::Label-->

                                        <!--begin::Cancel-->
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                              data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <!--end::Cancel-->

                                        <!--begin::Remove-->
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                              data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <!--end::Remove-->
                                    </div>
                                    <!--end::Image input-->

                                    <!--begin::Hint-->
                                    <div class="form-text">Allowed file types: jpg, jpeg, png, gif. Max size: 5MB</div>
                                    <!--end::Hint-->
                                </div>
                            </div>
                            <!-- File Upload -->

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Name</label>
                                <div class="col-lg-8">
                                    <input type="text" name="name" value="{{ old('name', $speaker->name ?? '') }}" class="form-control form-control-lg form-control-solid" />
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Line 1</label>
                                <div class="col-lg-8">
                                    <input type="text" name="line1" value="{{ old('line1', $speaker->line1 ?? '') }}" class="form-control form-control-lg form-control-solid" />
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Line 2</label>
                                <div class="col-lg-8">
                                    <input type="text" name="line2" value="{{ old('line2', $speaker->line2 ?? '') }}" class="form-control form-control-lg form-control-solid" />
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Line 3</label>
                                <div class="col-lg-8">
                                    <input type="text" name="line3" value="{{ old('line3', $speaker->line3 ?? '') }}" class="form-control form-control-lg form-control-solid" />
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a type="reset" href="{{route('speakers')}}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="kt_speaker_submit">
                                <span class="indicator-label">{{ isset($speaker) ? 'Save' : 'Save' }}</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
@endsection

@push('scripts')
    <script>
        "use strict";

        KTUtil.onDOMContentLoaded(function () {

            const form = document.querySelector('#kt_speaker_form');
            const submitBtn = document.querySelector('#kt_speaker_submit');
            const fileInput = document.querySelector('#filename');
            const imageRemovedInput = document.querySelector('#image_removed');
            const hasExistingImage = document.querySelector('#has_existing_image').value === '1';
            const isEditMode = {{ isset($speaker) ? 'true' : 'false' }};

            let imageWasRemoved = false;
            let newImageSelected = false;

            /* ===============================
               IMAGE REMOVE
            =============================== */
            document.querySelectorAll('[data-kt-image-input-action="remove"]').forEach(btn => {
                btn.addEventListener('click', function () {
                    imageWasRemoved = true;
                    newImageSelected = false;
                    imageRemovedInput.value = '1';

                    setTimeout(() => {
                        validator.revalidateField('filename');
                    }, 100);
                });
            });

            /* ===============================
               IMAGE CANCEL
            =============================== */
            document.querySelectorAll('[data-kt-image-input-action="cancel"]').forEach(btn => {
                btn.addEventListener('click', function () {
                    imageWasRemoved = false;
                    newImageSelected = false;
                    imageRemovedInput.value = '0';

                    setTimeout(() => {
                        validator.revalidateField('filename');
                    }, 100);
                });
            });

            /* ===============================
               NEW IMAGE SELECT
            =============================== */
            fileInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    newImageSelected = true;
                    imageWasRemoved = false;
                    imageRemovedInput.value = '0';
                }
            });

            /* ===============================
               FORM VALIDATION
            =============================== */
            const validator = FormValidation.formValidation(form, {
                fields: {
                    filename: {
                        validators: {
                            callback: {
                                message: 'Image is required',
                                callback: function () {

                                    // 🔹 EDIT MODE
                                    if (isEditMode && hasExistingImage) {

                                        if (imageWasRemoved && !newImageSelected) {
                                            return false;
                                        }

                                        if (!imageWasRemoved) {
                                            return true;
                                        }
                                    }

                                    // 🔹 ADD MODE or removed image
                                    if (!fileInput.files || fileInput.files.length === 0) {
                                        return false;
                                    }

                                    return true;
                                }
                            },
                            file: {
                                extension: 'jpg,jpeg,png,gif',
                                type: 'image/jpeg,image/png,image/gif',
                                maxSize: 5242880,
                                message: 'Only JPG/PNG/GIF up to 5MB allowed'
                            }
                        }
                    },

                    name: {
                        validators: {
                            notEmpty: {
                                message: 'Name is required'
                            }
                        }
                    },

                    line1: {
                        validators: {
                            notEmpty: {
                                message: 'Line 1 is required'
                            }
                        }
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.row'
                    })
                }
            });

            /* ===============================
               SUBMIT
            =============================== */
            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();

                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitBtn.setAttribute('data-kt-indicator', 'on');
                        submitBtn.disabled = true;
                        form.submit();
                    }
                });
            });

        });
    </script>

@endpush
