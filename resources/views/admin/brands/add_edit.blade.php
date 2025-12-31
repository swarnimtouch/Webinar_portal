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

            <!-- Create/Edit Brand Card -->
            <div class="card mb-5 mb-xl-10">


                <div id="kt_brand_create" class="collapse show">
                    <form method="POST"
                          action="{{ route('brand.store', $brand->id ?? null) }}"
                          id="kt_brand_form"
                          enctype="multipart/form-data">
                        @csrf
                        @if($brand->exists)
                            @method('PUT')
                        @endif

                        <input type="hidden" name="status" value="active">

                        <div class="card-body border-top p-9">
                            <!-- Title -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Title</label>
                                <div class="col-lg-8">
                                    <input type="text"
                                           name="title"
                                           id="title"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('title', $brand->title ?? '') }}"
                                           placeholder="Enter brand title" />
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
                                         style="background-image: url('{{ asset('assets/media/avatars/blank.png') }}')">

                                        <!--begin::Preview-->
                                        <div class="image-input-wrapper w-125px h-125px" id="brandImagePreview"
                                             style="background-image: url('{{ $brand->filename && $brand->type === 'image' ? asset('storage/brands/'.$brand->filename) : asset('assets/media/avatars/blank.png') }}')">
                                        </div>
                                        <!--end::Preview-->

                                        <!--begin::Label-->
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                               data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change file">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" name="filename" id="filename" accept="image/*,video/*" />
                                            <input type="hidden" name="brand_remove" />
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

                                    <div class="form-text">Allowed file types: jpg, jpeg, png, gif, mp4, mov, avi. Max size: 20MB</div>


                                    <!-- File Type Indicator -->
                                    <div id="fileTypeIndicator" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="{{route('brand')}}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="kt_brand_submit">
                                <span class="indicator-label">{{ $brand->exists ? 'Save' : 'Save' }}</span>
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
        var KTBrandForm = function () {
            var form;
            var submitButton;
            var validator;
            var fileInput;
            var fileTypeIndicator;
            var removeInput;

            var isEditMode = {{ $brand->exists ? 'true' : 'false' }};
            var blankImageUrl = "{{ asset('assets/media/avatars/blank.png') }}";
            var originalImageUrl = "{{ $brand->filename && $brand->type === 'image' ? asset('storage/brands/'.$brand->filename) : asset('assets/media/avatars/blank.png') }}";
            var fileRemoved = false;

            return {
                init: function () {
                    form = document.querySelector("#kt_brand_form");
                    submitButton = document.querySelector("#kt_brand_submit");
                    fileInput = document.querySelector("#filename");
                    fileTypeIndicator = document.querySelector("#fileTypeIndicator");
                    removeInput = document.querySelector("input[name='brand_remove']");

                    if (!form) {
                        console.error("Form not found");
                        return;
                    }

                    /* ===============================
                       FILE TYPE AUTO-DETECT
                    =============================== */
                    fileInput.addEventListener("change", function(e) {
                        var file = e.target.files[0];

                        if (!file) {
                            fileTypeIndicator.innerHTML = '';
                            $('#brandImagePreview').css('background-image', "url('" + blankImageUrl + "')");
                            fileRemoved = false;
                            removeInput.value = '';

                            // Trigger validation immediately
                            setTimeout(function() {
                                validator.revalidateField('filename');
                            }, 100);
                            return;
                        }

                        fileRemoved = false;
                        removeInput.value = '';
                        var fileType = file.type;

                        if (fileType.startsWith('image/')) {
                            fileTypeIndicator.innerHTML = '<span class="badge badge-light-primary">Type: Image</span>';

                            // Show image preview
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $('#brandImagePreview').css('background-image', 'url(' + e.target.result + ')');
                            };
                            reader.readAsDataURL(file);

                        } else if (fileType.startsWith('video/')) {
                            fileTypeIndicator.innerHTML = '<span class="badge badge-light-info">Type: Video</span>';
                            $('#brandImagePreview').css('background-image', "url('" + blankImageUrl + "')");
                        } else {
                            fileTypeIndicator.innerHTML = '<span class="badge badge-light-danger">Type: Unknown</span>';
                        }

                        // Trigger validation immediately after file selection
                        setTimeout(function() {
                            validator.revalidateField('filename');
                        }, 100);

                        console.log('File selected:', file.name, 'Type:', fileType);
                    });

                    /* ===============================
                       CANCEL / REMOVE BUTTONS
                    =============================== */
                    $(document).on('click', '[data-kt-image-input-action="cancel"]', function() {
                        console.log('File cancel clicked');

                        $('#filename').val('');
                        fileRemoved = false;
                        removeInput.value = '';
                        fileTypeIndicator.innerHTML = '';

                        // Restore original image
                        if (isEditMode && originalImageUrl !== blankImageUrl) {
                            $('#brandImagePreview').css('background-image', "url('" + originalImageUrl + "')");
                        } else {
                            $('#brandImagePreview').css('background-image', "url('" + blankImageUrl + "')");
                        }

                        // Trigger validation immediately
                        setTimeout(function() {
                            validator.revalidateField('filename');
                        }, 100);
                    });

                    $(document).on('click', '[data-kt-image-input-action="remove"]', function() {
                        console.log('File remove clicked');

                        $('#filename').val('');
                        fileRemoved = true;
                        removeInput.value = '1'; // Mark for removal
                        fileTypeIndicator.innerHTML = '';

                        // Show blank image
                        $('#brandImagePreview').css('background-image', "url('" + blankImageUrl + "')");

                        // Trigger validation immediately
                        setTimeout(function() {
                            validator.revalidateField('filename');
                        }, 100);
                    });

                    /* ===============================
                       FORM VALIDATION
                    =============================== */
                    var fileValidators = {};

                    if (!isEditMode) {
                        // Create mode - file required
                        fileValidators = {
                            notEmpty: {
                                message: "Image is required"
                            },
                            file: {
                                extension: 'jpg,jpeg,png,gif,webp,mp4,mov,avi,webm',
                                type: 'image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/webm',
                                maxSize: 20971520, // 20MB
                                message: 'Please select a valid image or video file (max 20MB)'
                            }
                        };
                    } else {
                        // Edit mode - file optional, but required if removed
                        fileValidators = {
                            callback: {
                                message: 'Please upload a new file',
                                callback: function(input) {
                                    // If file was removed, new file is required
                                    if (fileRemoved && input.value === '') {
                                        return false;
                                    }
                                    return true;
                                }
                            },
                            file: {
                                extension: 'jpg,jpeg,png,gif,webp,mp4,mov,avi,webm',
                                type: 'image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/webm',
                                maxSize: 20971520,
                                message: 'Please select a valid image or video file (max 20MB)'
                            }
                        };
                    }

                    validator = FormValidation.formValidation(form, {
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: "Title is required"
                                    }
                                }
                            },
                            filename: {
                                validators: fileValidators
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
                       SUBMIT
                    =============================== */
                    submitButton.addEventListener("click", function (e) {
                        e.preventDefault();

                        console.log('=== SUBMIT ===');
                        console.log('Edit Mode:', isEditMode);
                        console.log('File Removed:', fileRemoved);
                        console.log('Remove Input Value:', removeInput.value);
                        console.log('File Selected:', $('#filename')[0].files.length > 0 ? $('#filename')[0].files[0].name : 'none');

                        validator.validate().then(function (status) {
                            console.log('Validation Status:', status);

                            if (status === 'Valid') {
                                submitButton.setAttribute("data-kt-indicator", "on");
                                submitButton.disabled = true;

                                setTimeout(function() {
                                    form.submit();
                                }, 300);
                            } else {
                                var invalidFields = validator.getInvalidFields();
                                console.log('Invalid Fields:', invalidFields);

                                Swal.fire({
                                    text: "Please fill all required fields correctly",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        });
                    });

                    console.log("Brand form initialized | Edit:", isEditMode);
                }
            };
        }();

        KTUtil.onDOMContentLoaded(function () {
            KTBrandForm.init();
        });
    </script>
@endpush
