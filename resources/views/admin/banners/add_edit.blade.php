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

            <!-- Create/Edit Banner Card -->
            <div class="card mb-5 mb-xl-10">


                <div id="kt_banner_edit" class="collapse show">
                    <form method="POST"
                          action="{{ route('banners.store', $banner->id ?? null) }}"
                          id="kt_banner_form"
                          enctype="multipart/form-data">

                        @csrf
                        @if($banner->exists)
                            @method('PUT')
                        @endif

                        <input type="hidden" name="status" value="active">
                        <input type="hidden" name="current_type" id="current_type" value="{{ $banner->type ?? '' }}">

                        <div class="card-body border-top p-9">

                            <!-- Title -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Title</label>
                                <div class="col-lg-8">
                                    <input type="text" name="title" id="title"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('title', $banner->title ?? '') }}"
                                           placeholder="Enter banner title" />
                                </div>
                            </div>

                            <!-- Banner Type -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Type</label>
                                <div class="col-lg-8">
                                    <select name="type" id="type" class="form-select form-select-solid form-select-lg"
                                            data-control="select2" data-placeholder="Select a type" data-hide-search="true">
                                        <option></option>
                                        <option value="image" {{ old('type', $banner->type ?? '') === 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="video" {{ old('type', $banner->type ?? '') === 'video' ? 'selected' : '' }}>Video</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload Section -->
                            <div class="row mb-6" id="imageUploadSection"
                                 style="display: {{ old('type', $banner->type ?? '') === 'image' ? 'flex' : 'none' }};">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">
                                     Image
                                    @if(!$banner->exists || $banner->type !== 'image')
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-lg-8">
                                    <!--begin::Image input-->
                                    <div class="image-input image-input-outline" data-kt-image-input="true"
                                         style="background-image: url('{{ asset('assets/media/avatars/blank.png') }}')">

                                        <!--begin::Preview existing/new image-->
                                        <div class="image-input-wrapper w-125px h-125px" id="bannerImagePreview"
                                             style="background-image: url('{{ ($banner->type ?? '') === 'image' && $banner->filename ? asset('storage/banners/'.$banner->filename) : asset('assets/media/avatars/blank.png') }}')">
                                        </div>
                                        <!--end::Preview existing/new image-->

                                        <!--begin::Label-->
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                               data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" name="image_file" id="image_file" accept="image/*" />
                                            <input type="hidden" name="image_remove" />
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

                                    <div class="form-text">Allowed file types: jpg, jpeg, png, gif. Max size: 5MB</div>
                                    @if($banner->exists && $banner->type === 'image' && $banner->filename)
                                        <div class="form-text text-muted">Current image is displayed above. Upload new to replace.</div>
                                    @else
                                        <div class="form-text text-muted">Upload an image file</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Video Upload Section -->
                            <div class="row mb-6" id="videoUploadSection"
                                 style="display: {{ old('type', $banner->type ?? '') === 'video' ? 'flex' : 'none' }};">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">
                                     Video
                                    @if(!$banner->exists || $banner->type !== 'video')
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-lg-8">
                                    <input type="file" name="video_file" id="video_file"
                                           class="form-control form-control-lg form-control-solid" accept="video/*" />
                                    <div class="form-text">Allowed file types: mp4, mov, avi. Max size: 20MB</div>
                                    @if($banner->exists && $banner->type === 'video' && $banner->filename)
                                        <div class="form-text text-muted">Upload new video to replace current one</div>
                                    @else
                                        <div class="form-text text-muted">Upload a video file</div>
                                    @endif

                                    <!-- New Video Preview -->
                                    <div id="videoPreview" class="mt-3" style="display: none;">
                                        <label class="fw-bold fs-6 mb-2">New Video Preview:</label>
                                        <div class="position-relative overflow-hidden rounded" style="width:200px;height:200px;cursor:pointer;">
                                            <video id="videoPlayer"
                                                   width="100%"
                                                   height="100%"
                                                   muted
                                                   loop
                                                   style="object-fit:cover;">
                                                <source id="videoSource" src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            <div class="position-absolute top-50 start-50 translate-middle"
                                                 id="playIconOverlay"
                                                 style="pointer-events:none;transition:opacity 0.3s;">
                                                <i class="bi bi-play-circle-fill text-white" style="font-size: 3rem;opacity:0.8;"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Video Display -->
                                    @if($banner->exists && $banner->type === 'video' && $banner->filename)
                                        <div class="mt-3">
                                            <label class="fw-bold fs-6 mb-2">Current Video:</label>
                                            <div class="border rounded p-3">
                                                <div class="position-relative overflow-hidden rounded" style="width:200px;height:200px;">
                                                    <video width="100%" height="100%" muted loop style="object-fit:cover;" id="currentVideoPlayer">
                                                        <source src="{{ asset('storage/banners/'.$banner->filename) }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <div class="position-absolute top-50 start-50 translate-middle" id="currentPlayIcon" style="pointer-events:none;">
                                                        <i class="bi bi-play-circle-fill text-white" style="font-size: 3rem;opacity:0.8;"></i>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <span class="badge badge-light-info">Current: Video</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="{{route('banners')}}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="kt_banner_submit">
                                <span class="indicator-label">{{ $banner->exists ? 'Save' : 'Save' }}</span>
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
    <script src="{{ asset('assets/js/custom/widgets.js')}}"></script>
        <script>
            "use strict";
            var imageRemoved = false;
            var originalType = "{{ $banner->type ?? '' }}";
            var originalImageUrl = "{{ ($banner->type ?? '') === 'image' && $banner->filename
                ? asset('storage/banners/'.$banner->filename)
                : asset('assets/media/avatars/blank.png') }}";
            var blankImageUrl = "{{ asset('assets/media/avatars/blank.png') }}";
            var imageInputInstance = null;

            var KTBannerEdit = function () {
                var form;
                var submitButton;
                var validator;

                var isEditMode = {{ $banner->exists ? 'true' : 'false' }};
                var originalType = "{{ $banner->type ?? '' }}";

                return {
                    init: function () {

                        form = document.querySelector("#kt_banner_form");
                        submitButton = document.querySelector("#kt_banner_submit");

                        if (!form) {
                            console.error("Form not found");
                            return;
                        }

                        /* ===============================
                           SELECT2
                        =============================== */
                        $('#type').select2({
                            minimumResultsForSearch: Infinity
                        });

                        var blankImageUrl = "{{ asset('assets/media/avatars/blank.png') }}";
                        var originalImageUrl = "{{ ($banner->type ?? '') === 'image' && $banner->filename ? asset('storage/banners/'.$banner->filename) : asset('assets/media/avatars/blank.png') }}";

                        /* ===============================
                           FORM VALIDATION (STATIC)
                        =============================== */
                        validator = FormValidation.formValidation(form, {
                            fields: {
                                title: {
                                    validators: {
                                        notEmpty: {
                                            message: "Title is required"
                                        }
                                    }
                                },
                                type: {
                                    validators: {
                                        notEmpty: {
                                            message: "Type is required"
                                        }
                                    }
                                },
                                image_file: {
                                    validators: {
                                        notEmpty: {
                                            message: "Image file is required"
                                        },
                                        file: {
                                            extension: 'jpg,jpeg,png,gif,webp',
                                            type: 'image/jpeg,image/png,image/gif,image/webp',
                                            maxSize: 5242880,
                                            message: 'Invalid image file (max 5MB)'
                                        }
                                    }
                                },
                                video_file: {
                                    validators: {
                                        notEmpty: {
                                            message: "Video file is required"
                                        },
                                        file: {
                                            extension: 'mp4,mov,avi,webm',
                                            type: 'video/mp4,video/quicktime,video/x-msvideo,video/webm',
                                            maxSize: 20971520,
                                            message: 'Invalid video file (max 20MB)'
                                        }
                                    }
                                }
                            },
                            plugins: {
                                trigger: new FormValidation.plugins.Trigger(),
                                bootstrap: new FormValidation.plugins.Bootstrap5({
                                    rowSelector: '.row',
                                    eleInvalidClass: '',
                                    eleValidClass: ''
                                })
                            }
                        });

                        /* ===============================
                           INIT VALIDATION STATE
                        =============================== */
                        if (isEditMode) {
                            // Edit mode → file NOT required
                            validator.disableValidator('image_file', 'notEmpty');
                            validator.disableValidator('video_file', 'notEmpty');
                        } else {
                            // Create mode
                            if ($('#type').val() === 'image') {
                                validator.enableValidator('image_file', 'notEmpty');
                            }
                            if ($('#type').val() === 'video') {
                                validator.enableValidator('video_file', 'notEmpty');
                            }
                        }

                        /* ===============================
                           TYPE CHANGE
                        =============================== */
                        $('#type').on('select2:select', function (e) {

                            var selectedType = e.params.data.id;

                            if (selectedType === 'image') {

                                $('#imageUploadSection').show();
                                $('#videoUploadSection').hide();
                                $('#video_file').val('');
                                $('#videoPreview').hide();

                                // 🔥 Restore old image
                                if (isEditMode && originalType === 'image') {
                                    $('#bannerImagePreview').css(
                                        'background-image',
                                        "url('" + originalImageUrl + "')"
                                    );
                                    imageRemoved = false;
                                    validator.disableValidator('image_file', 'notEmpty');
                                } else {
                                    $('#bannerImagePreview').css(
                                        'background-image',
                                        "url('" + blankImageUrl + "')"
                                    );
                                    validator.enableValidator('image_file', 'notEmpty');
                                }

                                validator.disableValidator('video_file', 'notEmpty');
                                validator.resetField('video_file', true);

                            } else if (selectedType === 'video') {

                                $('#imageUploadSection').hide();
                                $('#videoUploadSection').show();
                                $('#image_file').val('');

                                validator.disableValidator('image_file', 'notEmpty');

                                if (isEditMode && originalType === 'video') {
                                    validator.disableValidator('video_file', 'notEmpty');
                                } else {
                                    validator.enableValidator('video_file', 'notEmpty');
                                }
                            }

                            validator.revalidateField('type');
                        });




                        /* ===============================
                           IMAGE CHANGE
                        =============================== */
                        $('#image_file').on('change', function () {

                            if (!this.files.length) {
                                validator.revalidateField('image_file');
                                return;
                            }
                            imageRemoved = false; // 🔥 NEW IMAGE selected

                            var reader = new FileReader();
                            reader.onload = function (e) {
                                $('#bannerImagePreview').css('background-image', 'url(' + e.target.result + ')');
                            };
                            reader.readAsDataURL(this.files[0]);

                            validator.revalidateField('image_file');
                        });
                        /* ===============================
                           IMAGE CANCEL / REMOVE (IMPORTANT)
                        =============================== */
                        $(document).on(
                            'click',
                            '[data-kt-image-input-action="cancel"], [data-kt-image-input-action="remove"]',
                            function () {

                                imageRemoved = true;

                                $('#bannerImagePreview').css(
                                    'background-image',
                                    "url('" + blankImageUrl + "')"
                                );

                                if ($('#type').val() === 'image') {
                                    validator.enableValidator('image_file', 'notEmpty');
                                    validator.revalidateField('image_file');
                                }
                            }
                        );




                        /* ===============================
                           VIDEO CHANGE
                        =============================== */
                        $('#video_file').on('change', function () {

                            if (!this.files.length) {
                                validator.revalidateField('video_file');
                                return;
                            }

                            var videoURL = URL.createObjectURL(this.files[0]);
                            $('#videoSource').attr('src', videoURL);
                            $('#videoPlayer')[0].load();
                            $('#videoPreview').fadeIn();

                            validator.revalidateField('video_file');
                        });

                        /* ===============================
                           SUBMIT
                        =============================== */
                        submitButton.addEventListener("click", function (e) {
                            e.preventDefault();

                            if (
                                isEditMode &&
                                $('#type').val() === 'image' &&
                                imageRemoved &&
                                !$('#image_file')[0].files.length
                            ) {
                                validator.enableValidator('image_file', 'notEmpty');
                                validator.revalidateField('image_file');
                                return;
                            }

                            validator.validate().then(function (status) {
                                if (status === 'Valid') {
                                    submitButton.setAttribute("data-kt-indicator", "on");
                                    submitButton.disabled = true;
                                    form.submit();
                                }
                            });
                        });
                        console.log("Banner form initialized | Edit:", isEditMode);
                    }
                };
            }();

            KTUtil.onDOMContentLoaded(function () {
                KTBannerEdit.init();
            });
        </script>

@endpush
