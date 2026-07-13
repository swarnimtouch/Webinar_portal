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

                <!-- Create/Edit Brand Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title fs-3 fw-bolder">{{$title}}</div>
                    </div>


                    <div id="kt_brand_create" class="collapse show">
                        <form method="POST"
                              action="{{ route('admin.brand.save', $brand->id ?? null) }}"
                              id="kt_brand_form"
                              enctype="multipart/form-data">
                            @csrf
                            @if($brand->exists)
                                @method('PUT')
                            @endif

                            <input type="hidden" name="status" value="active">

                            <div class="card-body border-top p-9">
                                @if(auth()->user()->type === 'admin')
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
                                                        {{ old('event_id', $brand->event_id ?? '') == $event->id ? 'selected' : '' }}>
                                                        {{ $event->name }}
                                                    </option>
                                                @endforeach


                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="event_id" value="{{ auth()->user()->event_id }}">
                                @endif
                                <!-- Title -->
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Title</label>
                                    <div class="col-lg-8">
                                        <input type="text"
                                               name="title"
                                               id="title"
                                               class="form-control form-control-lg form-control-solid"
                                               value="{{ old('title', $brand->title ?? '') }}"
                                               placeholder="Enter brand title"/>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div class="row mb-6" id="imageUploadSection">
                                    <label class="col-lg-4 col-form-label fw-bold fs-6">
                                        File
                                        @if(!$brand->exists)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <div class="col-lg-8">
                                        <!--begin::Image input-->
                                        <div class="image-input image-input-outline" data-kt-image-input="true"
                                             style="background-image: url('{{ asset('assets/media/no_image.png') }}')"
                                        >

                                            <!--begin::Preview-->
                                            <div class="image-input-wrapper w-125px h-125px" id="brandImagePreview"
                                                 style="background-image: url('{{ $brand->filename && $brand->type === 'image' ? asset('storage/brands/'.$brand->filename) : asset('assets/media/no_image.png') }}')"
                                            >
                                            </div>
                                            <!--end::Preview-->

                                            <!--begin::Label-->
                                            <label
                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                title="Change file">
                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                <input type="file" name="filename" id="filename"
                                                       accept="image/*,video/*"/>
                                                <input type="hidden" name="brand_remove"/>
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

                                        <div class="form-text">Allowed file types: jpg, jpeg, png, gif.
                                            Max size: 20MB
                                        </div>


                                        <!-- File Type Indicator -->
                                        <div id="fileTypeIndicator" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <a href="{{route('admin.brand')}}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="kt_brand_submit">
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

                const KTBrandEdit = (() => {

                    let form, submitBtn, validator;
                    const isEdit = {{ $brand->exists ? 'true' : 'false' }};
                    const blankImage = "{{ asset('assets/media/no_image.png') }}";
                    const originalImage = "{{ $brand->filename && $brand->type === 'image'
                        ? asset('storage/brands/'.$brand->filename)
                        : asset('assets/media/no_image.png') }}";
                    const isAdmin = {{ auth()->user()->type === 'admin' ? 'true' : 'false' }};


                    let fileRemoved = false;

                    const init = () => {

                        form = document.getElementById('kt_brand_form');
                        submitBtn = document.getElementById('kt_brand_submit');

                        if (!form) return;

                        const fileInput = document.getElementById('filename');
                        const removeInput = document.querySelector("input[name='brand_remove']");

                        validator = FormValidation.formValidation(form, {
                            fields: {
                                title: {
                                    validators: {
                                        notEmpty: {message: 'Title is required'}
                                    }
                                },
                                ...(isAdmin && {
                                    event_id: {
                                        validators: {
                                            notEmpty: {message: 'Event is required'}
                                        }
                                    }
                                }),
                                filename: {
                                    validators: {
                                        callback: {
                                            message: 'Image is required',
                                            callback: function (input) {
                                                if (!isEdit && input.value === '') return false;
                                                if (isEdit && fileRemoved && input.value === '') return false;
                                                return true;
                                            }
                                        },
                                        file: {
                                            extension: 'jpg,jpeg,png,gif,webp,mp4,mov,avi,webm',
                                            type: 'image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/webm',
                                            maxSize: 20971520,
                                            message: 'Invalid Image (max 20MB)'
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
                        if (isAdmin && $('#event_id').length) {
                            $('#event_id').select2();

                            $('#event_id').on('change', function () {
                                validator.revalidateField('event_id');
                            });
                        }
                        fileInput.addEventListener('change', function () {
                            if (!this.files.length) return;

                            fileRemoved = false;
                            removeInput.value = '';

                            const file = this.files[0];
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = e => {
                                    $('#brandImagePreview').css('background-image', `url('${e.target.result}')`);
                                };
                                reader.readAsDataURL(file);
                            } else {
                                $('#brandImagePreview').css('background-image', `url('${blankImage}')`);
                            }

                            validator.revalidateField('filename');
                        });

                        $(document).on(
                            'click',
                            '[data-kt-image-input-action="remove"], [data-kt-image-input-action="cancel"]',
                            () => {
                                $('#filename').val('');
                                fileRemoved = true;
                                document.querySelector("input[name='brand_remove']").value = '1';

                                $('#brandImagePreview').css(
                                    'background-image',
                                    `url('${blankImage}')`
                                );

                                validator.revalidateField('filename');
                            }
                        );


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
                    KTBrandEdit.init();
                });
            </script>

    @endpush
