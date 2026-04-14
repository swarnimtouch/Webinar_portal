@extends('layouts.admin')

@push('style')
    <style>
        .ql-container {
            min-height: 100px;
        }
    </style>
@endpush

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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="card-title fs-3 fw-bolder">{{ $title }}</div>
                    </div>
                    <div class="card-body border-top p-9">
                        <form method="POST"
                              action="{{ route('admin.certificate.save', $certificate->id ?? null) }}"
                              id="kt_certificate_form"
                              enctype="multipart/form-data">

                            @csrf
                            @if($certificate->exists)
                                @method('PUT')
                            @endif

                            <!-- Name -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Certificate Name</label>
                                <div class="col-lg-8">
                                    <input type="text" name="name"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('name', $certificate->name ?? '') }}"
                                           placeholder="Enter certificate name"/>
                                </div>
                            </div>

                            <!-- Background Image -->
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label {{ $certificate->exists ? '' : 'required' }} fw-bold fs-6">
                                    Background Image
                                </label>
                                <div class="col-lg-8">
                                    @if($certificate->exists && $certificate->background_image)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $certificate->background_image) }}"
                                                 alt="Current Background"
                                                 class="rounded border"
                                                 style="height: 120px; object-fit: cover;">
                                            <div class="form-text">Current image. Upload new to replace.</div>
                                        </div>
                                    @endif
                                    <input type="file" name="background_image" id="background_image"
                                           class="form-control form-control-lg form-control-solid"
                                           accept="image/jpeg,image/png,image/webp"/>
                                    <div class="form-text">Accepted: JPG, PNG, WEBP. Max size: 5MB</div>
                                </div>
                            </div>

                            <!-- Font File -->
                            {{-- Font File - TEXT ki jagah FILE UPLOAD --}}
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Font File</label>
                                <div class="col-lg-8">
                                    @if($certificate->exists && $certificate->font_file)
                                        <div class="mb-3">
                                        <span class="badge badge-light-info fs-7">
                                            <i class="bi bi-file-earmark-font me-1"></i>
                                            {{ basename($certificate->font_file) }}
                                        </span>
                                            <div class="form-text">Current font. Upload new to replace.</div>
                                        </div>
                                    @endif
                                    <input type="file" name="font_file" id="font_file"
                                           class="form-control form-control-lg form-control-solid"
                                           accept=".ttf,.otf,.woff,.woff2"/>
                                    <div class="form-text">Accepted: TTF, OTF, WOFF, WOFF2. Leave empty to keep
                                        current.
                                    </div>
                                </div>
                            </div>

                            <!-- Font Size & Font Color -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Font Size (px)</label>
                                <div class="col-lg-8">
                                    <input type="number" name="font_size"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('font_size', $certificate->font_size ?? 30) }}"
                                           min="1" max="300"
                                           placeholder="e.g. 30"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Font Color</label>
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="color" id="font_color_picker"
                                               value="{{ old('font_color', $certificate->font_color ?? '#000000') }}"
                                               class="form-control form-control-color"
                                               style="width: 60px; height: 45px; padding: 2px 4px; cursor:pointer;">
                                        <input type="text" name="font_color" id="font_color"
                                               class="form-control form-control-lg form-control-solid"
                                               value="{{ old('font_color', $certificate->font_color ?? '#000000') }}"
                                               placeholder="#000000"/>
                                    </div>
                                    <div class="form-text">Enter hex color code or use the color picker</div>
                                </div>
                            </div>

                            <!-- Is Bold -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Bold Text</label>
                                <div class="col-lg-8 d-flex align-items-center">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox"
                                               name="is_bold" id="is_bold" value="1"
                                            {{ old('is_bold', $certificate->is_bold ?? false) ? 'checked' : '' }}/>
                                        <label class="form-check-label" for="is_bold">
                                            Make participant name bold
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Position: start_x, end_x, y -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Text Position (X)</label>
                                <div class="col-lg-8">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label text-muted">Start X</label>
                                            <input type="number" name="start_x"
                                                   class="form-control form-control-lg form-control-solid"
                                                   value="{{ old('start_x', $certificate->start_x ?? 0) }}"
                                                   min="0" placeholder="0"/>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-muted">End X</label>
                                            <input type="number" name="end_x"
                                                   class="form-control form-control-lg form-control-solid"
                                                   value="{{ old('end_x', $certificate->end_x ?? 0) }}"
                                                   min="0" placeholder="0"/>
                                        </div>
                                    </div>
                                    <div class="form-text">Horizontal range where the name text will be rendered</div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Text Position (Y)</label>
                                <div class="col-lg-8">
                                    <input type="number" name="y"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('y', $certificate->y ?? 0) }}"
                                           min="0" placeholder="0"/>
                                    <div class="form-text">Vertical position where the name text will be rendered</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('admin.certificate') }}"
                           class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="kt_certificate_submit">
                            <span class="indicator-label">Save</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        "use strict";

        const KTCertificateEdit = (() => {

            let form, submitBtn, validator;

            const init = () => {
                form = document.getElementById('kt_certificate_form');
                submitBtn = document.getElementById('kt_certificate_submit');

                if (!form) return;

                $('#status').select2({minimumResultsForSearch: Infinity});

                /* ===== COLOR PICKER SYNC ===== */
                const colorPicker = document.getElementById('font_color_picker');
                const colorText = document.getElementById('font_color');

                colorPicker.addEventListener('input', () => {
                    colorText.value = colorPicker.value;
                });

                colorText.addEventListener('input', () => {
                    const val = colorText.value;
                    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        colorPicker.value = val;
                    }
                });

                /* ===== FORM VALIDATION ===== */
                validator = FormValidation.formValidation(form, {
                    fields: {
                        name: {
                            validators: {
                                notEmpty: {message: 'Certificate name is required'},
                                stringLength: {min: 2, max: 255, message: 'Name must be between 2 and 255 characters'}
                            }
                        },
                        font_size: {
                            validators: {
                                notEmpty: {message: 'Font size is required'},
                                numeric: {message: 'Font size must be a number'},
                                between: {min: 1, max: 300, message: 'Font size must be between 1 and 300'}
                            }
                        },
                        font_color: {
                            validators: {
                                notEmpty: {message: 'Font color is required'},
                                regexp: {regexp: /^#[0-9A-Fa-f]{6}$/, message: 'Enter a valid hex color (e.g. #FF0000)'}
                            }
                        },
                        start_x: {
                            validators: {
                                notEmpty: {message: 'Start X is required'},
                                numeric: {message: 'Must be a number'}
                            }
                        },
                        end_x: {
                            validators: {
                                notEmpty: {message: 'End X is required'},
                                numeric: {message: 'Must be a number'}
                            }
                        },
                        y: {
                            validators: {
                                notEmpty: {message: 'Y position is required'},
                                numeric: {message: 'Must be a number'}
                            }
                        },
                        status: {
                            validators: {
                                notEmpty: {message: 'Status is required'}
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({rowSelector: '.row'})
                    }
                });

                /* ===== SUBMIT ===== */
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

        KTUtil.onDOMContentLoaded(() => KTCertificateEdit.init());
    </script>
@endpush
