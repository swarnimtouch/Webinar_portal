@extends('layouts.admin')


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

                <!-- Create/Edit Speaker Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title fs-3 fw-bolder">{{$title}}</div>
                    </div>
                    <div id="kt_speaker_wrapper" class="collapse show">
                        <form method="POST"
                              action="{{ route('admin.speakers.save', isset($speaker) ? $speaker->id : null) }}"
                              id="kt_speaker_form"
                              enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="type" id="type" value="">
                            <input type="hidden" name="status"
                                   value="{{ isset($speaker) ? $speaker->status : 'active' }}">
                            <input type="hidden" name="image_removed" id="image_removed" value="0">
                            <input type="hidden" name="has_existing_image" id="has_existing_image"
                                   value="{{ isset($speaker) && $speaker->filename ? '1' : '0' }}">

                            <div class="card-body border-top p-9">
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Event</label>
                                    <div class="col-lg-8">
                                        <select name="event_id" id="event_id"
                                                class="form-select form-select-solid form-select-lg"
                                                data-control="select2" data-placeholder="Select a Event "
                                                data-hide-search="true">
                                            <option value="Select Event " disabled>
                                                Select Event
                                            </option>
                                            <option value="" disabled selected>Select Event</option>

                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}"
                                                    {{ old('event_id', $speaker->event_id ?? '') == $event->id ? 'selected' : '' }}>
                                                    {{ $event->name }}
                                                </option>
                                            @endforeach


                                        </select>
                                    </div>
                                </div>
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
                                            <label
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                title="Change image">
                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                <!--begin::Inputs-->
                                                <input type="file" name="filename" id="filename" accept="image/*"/>
                                                <input type="hidden" name="image_remove"/>
                                                <!--end::Inputs-->
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

                                        <!--begin::Hint-->
                                        <div class="form-text">Allowed file types: jpg, jpeg, png, gif. Max size: 5MB
                                        </div>
                                        <!--end::Hint-->
                                    </div>
                                </div>
                                <!-- File Upload -->

                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Name</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="name" value="{{ old('name', $speaker->name ?? '') }}"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Enter Name"/>
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Line 1</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="line1"
                                               value="{{ old('line1', $speaker->line1 ?? '') }}"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Enter Line1"/>
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label  fw-bold fs-6">Line 2</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="line2"
                                               value="{{ old('line2', $speaker->line2 ?? '') }}"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Enter Line2"/>
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label  fw-bold fs-6">Line 3</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="line3"
                                               value="{{ old('line3', $speaker->line3 ?? '') }}"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Enter Line3"/>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <a type="reset" href="{{route('admin.speakers')}}"
                                   class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="kt_speaker_submit">
                                    <span class="indicator-label">Save</span>
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

        @endsection

        @push('scripts')
            <script>
                "use strict";

                const KTSpeakerEdit = (() => {

                    let form, submitBtn, validator;
                    const isEdit = {{ isset($speaker) ? 'true' : 'false' }};
                    const blankImage = "{{ asset('assets/media/avatars/blank.png') }}";

                    let imageRemoved = false;

                    const init = () => {

                        form = document.getElementById('kt_speaker_form');
                        submitBtn = document.getElementById('kt_speaker_submit');

                        if (!form) return;

                        const fileInput = document.getElementById('filename');
                        const imageRemovedInput = document.getElementById('image_removed');

                        validator = FormValidation.formValidation(form, {
                            fields: {
                                name: {
                                    validators: {
                                        notEmpty: {message: 'Name is required'}
                                    }
                                },
                                event_id: {
                                    validators: {
                                        notEmpty: {message: 'Event is required'}
                                    }
                                },
                                line1: {
                                    validators: {
                                        notEmpty: {message: 'Line 1 is required'}
                                    }
                                },
                                filename: {
                                    validators: {
                                        callback: {
                                            message: 'Image is required',
                                            callback: function () {
                                                if (isEdit && !imageRemoved) return true;
                                                return fileInput.files && fileInput.files.length > 0;
                                            }
                                        },
                                        file: {
                                            extension: 'jpg,jpeg,png,gif',
                                            type: 'image/jpeg,image/png,image/gif',
                                            maxSize: 5242880,
                                            message: 'Only JPG/PNG/GIF up to 5MB allowed'
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

                        fileInput.addEventListener('change', function () {
                            if (!this.files.length) return;
                            imageRemoved = false;
                            imageRemovedInput.value = '0';
                            validator.revalidateField('filename');
                        });

                        document.querySelectorAll(
                            '[data-kt-image-input-action="cancel"], [data-kt-image-input-action="remove"]'
                        ).forEach(btn => {
                            btn.addEventListener('click', () => {
                                imageRemoved = true;
                                imageRemovedInput.value = '1';
                                fileInput.value = '';
                                document.querySelectorAll('.image-input-wrapper').forEach(el => {
                                    el.style.backgroundImage = `url('${blankImage}')`;
                                });
                                validator.revalidateField('filename');
                            });
                        });

                        submitBtn.addEventListener('click', e => {
                            e.preventDefault();
                            validator.validate().then(status => {
                                if (status !== 'Valid') return;
                                submitBtn.setAttribute('data-kt-indicator', 'on');
                                submitBtn.disabled = true;
                                form.submit();
                            });
                        });
                    };

                    return {init};

                })();

                KTUtil.onDOMContentLoaded(() => {
                    KTSpeakerEdit.init();
                });
            </script>


    @endpush
