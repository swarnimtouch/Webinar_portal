@extends('layouts.admin')
@push('style')
    <style>
        .video-preview-player {
            width: 200px;
            height: 200px;
            cursor: pointer;
        }
    </style>
@endpush
@section('content')
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
                <div class="card">

                    <div class="card-header">
                        <div class="card-title fs-3 fw-bolder">{{$title}}</div>
                    </div>
                    <div class="card-body border-top p-9">
                        <form method="POST"
                              action="{{ route('admin.banners.save', $banner->id ?? null) }}"
                              id="kt_banner_form"
                              enctype="multipart/form-data">

                            @csrf
                            @if($banner->exists)
                                @method('PUT')
                            @endif

                            <input type="hidden" name="current_type" id="current_type"
                                   value="{{ $banner->type ?? '' }}">


                            <!-- Title -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Title</label>
                                <div class="col-lg-8">
                                    <input type="text" name="title" id="title"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('title', $banner->title ?? '') }}"
                                           placeholder="Enter banner title"/>
                                </div>
                            </div>

                            <!-- Banner Type -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Type</label>
                                <div class="col-lg-8">
                                    <select name="type" id="type"
                                            class="form-select form-select-solid form-select-lg"
                                            data-control="select2" data-placeholder="Select a type"
                                            data-hide-search="true">
                                        <option
                                            value="image" {{ old('type', $banner->type ?? '') === 'image' ? 'selected' : '' }}>
                                            Image
                                        </option>
                                        <option
                                            value="video" {{ old('type', $banner->type ?? '') === 'video' ? 'selected' : '' }}>
                                            Video
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload Section -->
                            <div
                                class="row mb-6 {{ $banner->exists && $banner->type != 'image' ? 'd-none':'' }}"
                                id="imageUploadSection">
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
                                             style="background-image: url('{{ $banner->media_url }}')">
                                        </div>
                                        <!--end::Preview existing/new image-->

                                        <!--begin::Label-->
                                        <label
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Change image">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" name="image_file" id="image_file" accept="image/*"/>
                                            <input type="hidden" name="image_remove"/>
                                        </label>
                                        <!--end::Label-->

                                        <!--begin::Cancel-->
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                            title="Cancel">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <!--end::Cancel-->

                                        <!--begin::Remove-->
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remove">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <!--end::Remove-->
                                    </div>
                                    <!--end::Image input-->

                                    <div class="form-text">Allowed file types: jpg, jpeg, png, gif. Max size: 5MB
                                    </div>
                                    @if($banner->exists && $banner->type === 'image' && $banner->filename)
                                        <div class="form-text text-muted">Current image is displayed above. Upload
                                            new to replace.
                                        </div>
                                    @else
                                        <div class="form-text text-muted">Upload an image file</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Video Upload Section -->
                            <div
                                class="row mb-6 {{ ($banner->exists && $banner->type != 'video') || !isset($banner->type) ? 'd-none':'' }}"
                                id="videoUploadSection">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">
                                    Video
                                    @if(!$banner->exists || $banner->type !== 'video')
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-lg-8">
                                    <input type="file" name="video_file" id="video_file"
                                           class="form-control form-control-lg form-control-solid"
                                           accept="video/*"/>
                                    <div class="form-text">Allowed file types: mp4, mov, avi. Max size: 20MB</div>
                                    @if($banner->exists && $banner->type === 'video' && $banner->filename)
                                        <div class="form-text text-muted">Upload new video to replace current one
                                        </div>
                                    @else
                                        <div class="form-text text-muted">Upload a video file</div>
                                    @endif

                                    <!-- New Video Preview -->
                                    <div id="videoPreview" class="mt-3 d-none">
                                        <label class="fw-bold fs-6 mb-2">New Video Preview:</label>
                                        <div class="position-relative overflow-hidden rounded video-preview-player">
                                            <video id="videoPlayer"
                                                   width="50%"
                                                   height="100%"
                                                   muted
                                                   loop>
                                                <source id="videoSource" src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            <div class="position-absolute top-50 start-50 translate-middle"
                                                 id="playIconOverlay">
                                                <i class="bi bi-play-circle-fill text-white"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Video Display -->
                                    @if($banner->exists && $banner->type === 'video' && $banner->filename)
                                        <div class="mt-3">
                                            <label class="fw-bold fs-6 mb-2">Video:</label>
                                            <div class="p-3">
                                                <div class="position-relative overflow-hidden rounded">
                                                    <video width="50%" height="100%" muted loop
                                                           id="currentVideoPlayer">
                                                        <source
                                                            src="{{ $banner->media_url }}"
                                                            type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <div class="position-absolute top-50 start-50 translate-middle"
                                                         id="currentPlayIcon">
                                                        <i class="bi bi-play-circle-fill text-white"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>


                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{route('admin.banners')}}"
                           class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="kt_banner_submit">
                            <span class="indicator-label">Save</span>
                            <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endsection

        @push('scripts')
            <script>
                "use strict";

                const KTBannerEdit = (() => {

                    let form, submitBtn, validator;
                    const isEdit = {{ $banner->exists ? 'true' : 'false' }};
                    const originalType = "{{ $banner->type ?? '' }}";

                    let imageRemoved = false;
                    let videoRemoved = false;

                    const blankImage = "{{ asset('assets/media/avatars/blank.png') }}";
                    const originalImage = "{{ $banner->type === 'image' && $banner->filename
        ? asset('storage/banners/'.$banner->filename)
        : asset('assets/media/avatars/blank.png') }}";


                    const showImageSection = () => {
                        $('#imageUploadSection').removeClass('d-none');
                        $('#videoUploadSection').addClass('d-none');
                    };

                    const showVideoSection = () => {
                        $('#videoUploadSection').removeClass('d-none');
                        $('#imageUploadSection').addClass('d-none');
                    };

                    const syncTypeValidation = (type) => {

                        if (type === 'image') {

                            $('#imageUploadSection').removeClass('d-none');
                            $('#videoUploadSection').addClass('d-none');

                            validator.disableValidator('video_file');
                            validator.resetField('video_file', true);

                            if (!isEdit || originalType !== 'image' || imageRemoved) {
                                validator.enableValidator('image_file', 'notEmpty');
                            } else {
                                validator.disableValidator('image_file', 'notEmpty');
                            }
                        }

                        if (type === 'video') {

                            $('#videoUploadSection').removeClass('d-none');
                            $('#imageUploadSection').addClass('d-none');

                            validator.disableValidator('image_file');
                            validator.resetField('image_file', true);

                            if (!isEdit || originalType !== 'video' || videoRemoved) {
                                validator.enableValidator('video_file', 'notEmpty');
                            } else {
                                validator.disableValidator('video_file', 'notEmpty');
                            }
                        }
                    };

                    const init = () => {

                        form = document.getElementById('kt_banner_form');
                        submitBtn = document.getElementById('kt_banner_submit');

                        if (!form) return;

                        $('#type').select2({minimumResultsForSearch: Infinity});

                        validator = FormValidation.formValidation(form, {
                            fields: {
                                title: {
                                    validators: {
                                        notEmpty: {message: 'Title is required'}
                                    }
                                },
                                type: {
                                    validators: {
                                        notEmpty: {message: 'Type is required'}
                                    }
                                },
                                image_file: {
                                    validators: {
                                        notEmpty: {message: 'Image is required'},
                                        file: {
                                            extension: 'jpg,jpeg,png,webp',
                                            type: 'image/jpeg,image/png,image/webp',
                                            maxSize: 5242880,
                                            message: 'Invalid image (max 5MB)'
                                        }
                                    }
                                },
                                video_file: {
                                    validators: {
                                        notEmpty: {message: 'Video is required'},
                                        file: {
                                            extension: 'mp4,mov,webm',
                                            type: 'video/mp4,video/quicktime,video/webm',
                                            maxSize: 20971520,
                                            message: 'Invalid video (max 20MB)'
                                        }
                                    }
                                }
                            },
                            plugins: {
                                trigger: new FormValidation.plugins.Trigger(),
                                bootstrap5: new FormValidation.plugins.Bootstrap5({
                                    rowSelector: '.row'
                                })
                            }
                        });

                        if (isEdit) {
                            validator.disableValidator('image_file', 'notEmpty');
                            validator.disableValidator('video_file', 'notEmpty');
                        }

                        syncTypeValidation($('#type').val());
                        $('#type').on('change', function () {
                            syncTypeValidation(this.value);
                        });

                        $('#image_file').on('change', function () {
                            if (!this.files.length) return;

                            imageRemoved = false;
                            previewImage(this.files[0]);
                            validator.revalidateField('image_file');
                        });

                        $(document).on(
                            'click',
                            '[data-kt-image-input-action="remove"], [data-kt-image-input-action="cancel"]',
                            () => {
                                imageRemoved = true;
                                showImagePreview(blankImage);
                                validator.revalidateField('image_file');
                            }
                        );

                        $('#video_file').on('change', function () {
                            if (!this.files.length) return;

                            const url = URL.createObjectURL(this.files[0]);
                            $('#videoSource').attr('src', url);
                            $('#videoPlayer')[0].load();
                            $('#videoPreview').removeClass('d-none');

                            validator.revalidateField('video_file');
                        });

                        submitBtn.addEventListener('click', e => {
                            e.preventDefault();

                            if (isEdit && $('#type').val() === 'image' && imageRemoved && !$('#image_file')[0].files.length) {
                                validator.enableValidator('image_file', 'notEmpty');
                            }

                            validator.validate().then(status => {
                                if (status !== 'Valid') return;

                                submitBtn.setAttribute('data-kt-indicator', 'on');
                                submitBtn.disabled = true;
                                form.submit();
                            });
                        });
                    };

                    const previewImage = file => {
                        const reader = new FileReader();
                        reader.onload = e => showImagePreview(e.target.result);
                        reader.readAsDataURL(file);
                    };

                    const showImagePreview = url => {
                        $('#bannerImagePreview').css('background-image', `url('${url}')`);
                        syncTypeValidation($('#type').val());
                    };

                    return {init};

                })();

                KTUtil.onDOMContentLoaded(() => {
                    KTBannerEdit.init();
                });
            </script>
    @endpush
