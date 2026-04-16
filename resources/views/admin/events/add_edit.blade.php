@extends('layouts.admin')
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
                        <div class="card-title fs-3 fw-bolder">{{$title}}</div>
                    </div>
                    <div class="card-body border-top p-9">
                        <form method="POST"
                              action="{{ route('admin.events.save',[$event->id??null]) }}"
                              id="kt_event_form"
                              enctype="multipart/form-data">
                            @csrf


                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Domain</label>
                                <div class="col-lg-8">
                                    <div class="position-relative">
                                        <input type="text" name="domain" id="domain"
                                               class="form-control form-control-lg form-control-solid"
                                               value="{{ old('domain', $event->domain ?? '') }}"
                                               placeholder="Enter Domain"/>
                                        <div
                                            class="position-absolute translate-middle-y top-50 end-0 me-5 text-muted fw-bold">
                                            .doctorly.com
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Name</label>
                                <div class="col-lg-8">
                                    <input type="text" name="name" id="name"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('name', $event->name ?? '') }}"
                                           placeholder="Enter name"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Email</label>
                                <div class="col-lg-8">
                                    <input type="email" name="email" id="email"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('email', $event->email ?? '') }}"
                                           placeholder="Enter email"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Phone</label>
                                <div class="col-lg-8">
                                    <input type="text" name="phone" id="phone"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('phone', $event->phone ?? '') }}"
                                           placeholder="Enter phone"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-bold fs-6 {{ !isset($event->id) ? 'required' : '' }}">
                                    Favicon
                                </label>
                                <div class="col-lg-8">
                                    @if($event->getRawOriginal('favicon') && $event->getRawOriginal('favicon') !== null)
                                        <div class="mb-3 d-flex align-items-center gap-3">
                                            <img id="favicon-current"
                                                 src="{{$event->favicon}}"
                                                 alt="Current Favicon"
                                                 class="rounded border"
                                                 style="width:48px;height:48px;object-fit:contain;background:#f5f8fa;">
                                            <span class="text-muted fs-7">Current favicon</span>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center gap-4">
                                        <input type="file" name="favicon" id="favicon_input"
                                               accept=".jpg,.jpeg,.png,.webp"
                                               class="d-none">

                                        <div id="favicon-preview-box"
                                             class="border rounded d-flex align-items-center justify-content-center bg-light-primary"
                                             style="width:80px;height:80px;cursor:pointer;flex-shrink:0;"
                                             onclick="document.getElementById('favicon_input').click()">
                                            <img id="favicon-preview-img"
                                                 src="#" alt=""
                                                 class="rounded d-none"
                                                 style="width:72px;height:72px;object-fit:contain;">
                                            <span id="favicon-preview-placeholder" class="text-center text-muted p-1">
                                                <i class="bi bi-image fs-2x d-block mb-1"></i>
                                                <small>Click to upload</small>
                                            </span>
                                        </div>

                                        <div>
                                            <button type="button"
                                                    class="btn btn-sm btn-light-primary mb-2"
                                                    onclick="document.getElementById('favicon_input').click()">
                                                <i class="bi bi-upload me-1"></i>
                                                {{ isset($event->favicon) && $event->getRawOriginal('favicon') !== null ? 'Change Favicon' : 'Upload Favicon' }}
                                            </button>
                                            <div class="text-muted fs-7">Accepted: JPG, PNG, WEBP &bull; Max 5MB</div>
                                            <div class="text-muted fs-7">Recommended: 32×32 or 64×64 px</div>
                                            <div id="favicon-filename" class="text-primary fs-7 mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-bold fs-6 {{ !isset($event->id) ? 'required' : '' }}">
                                    Logo
                                </label>
                                <div class="col-lg-8">
                                    @if(isset($event->logo) && $event->getRawOriginal('logo') !== null)
                                        <div class="mb-3 d-flex align-items-center gap-3">
                                            <img id="logo-current"
                                                 src="{{ $event->logo }}"
                                                 alt="Current Logo"
                                                 class="rounded border"
                                                 style="max-width:160px;max-height:64px;object-fit:contain;background:#f5f8fa;padding:4px;">
                                            <span class="text-muted fs-7">Current logo</span>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center gap-4">
                                        <input type="file" name="logo" id="logo_input"
                                               accept=".jpg,.jpeg,.png,.webp"
                                               class="d-none">

                                        <div id="logo-preview-box"
                                             class="border rounded d-flex align-items-center justify-content-center bg-light-primary"
                                             style="width:160px;height:80px;cursor:pointer;flex-shrink:0;"
                                             onclick="document.getElementById('logo_input').click()">
                                            <img id="logo-preview-img"
                                                 src="#" alt=""
                                                 class="rounded d-none"
                                                 style="max-width:150px;max-height:72px;object-fit:contain;">
                                            <span id="logo-preview-placeholder" class="text-center text-muted p-2">
                                                <i class="bi bi-image fs-2x d-block mb-1"></i>
                                                <small>Click to upload</small>
                                            </span>
                                        </div>

                                        <div>
                                            <button type="button"
                                                    class="btn btn-sm btn-light-primary mb-2"
                                                    onclick="document.getElementById('logo_input').click()">
                                                <i class="bi bi-upload me-1"></i>
                                                {{ isset($event->logo) && $event->getRawOriginal('logo') !== null ? 'Change Logo' : 'Upload Logo' }}
                                            </button>
                                            <div class="text-muted fs-7">Accepted: JPG, PNG, WEBP &bull; Max 5MB</div>
                                            <div class="text-muted fs-7">Recommended: 200×60 px or wider</div>
                                            <div id="logo-filename" class="text-primary fs-7 mt-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Footer Text</label>
                                <div class="col-lg-8">
                                    <input type="text" name="footer_text" id="footer_text"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('footer_text', $event->footer_text ?? '') }}"
                                           placeholder="Enter footer text"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Player Type</label>
                                <div class="col-lg-8">
                                    <select name="player_type" id="player_type"
                                            class="form-select form-select-solid form-select-lg"
                                            data-control="select2" data-placeholder="Select player type"
                                            data-hide-search="true">
                                        <option value="">Select player type</option>
                                        <option
                                            value="youtube" {{ old('player_type', $event->player_type ?? '') === 'youtube' ? 'selected' : '' }}>
                                            YouTube
                                        </option>
                                        <option
                                            value="vimeo" {{ old('player_type', $event->player_type ?? '') === 'vimeo'   ? 'selected' : '' }}>
                                            Vimeo
                                        </option>
                                        <option
                                            value="other" {{ old('player_type', $event->player_type ?? '') === 'other'   ? 'selected' : '' }}>
                                            Other
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Player ID / URL</label>
                                <div class="col-lg-8">
                                    <input type="text" name="player_id" id="player_id"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('player_id', $event->player_id ?? '') }}"
                                           placeholder="Enter YouTube/Vimeo ID or full URL"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Video Iframe Code</label>
                                <div class="col-lg-8">
                                    <textarea name="player_iframe" id="player_iframe"
                                              class="form-control form-control-lg form-control-solid"
                                              rows="5"
                                              placeholder="Auto-generated iframe code (you can also edit manually)">{{ old('player_iframe', $event->player_iframe ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Video Preview</label>
                                <div class="col-lg-8">
                                    <div id="video-preview" class="border rounded p-5 bg-light-primary"
                                         style="min-height:200px;">
                                        <div class="text-center text-muted" id="preview-placeholder">
                                            <i class="bi bi-play-circle fs-3x mb-3"></i>
                                            <p class="mb-0">Video preview will appear here</p>
                                        </div>
                                        <div id="preview-content" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Publish Date</label>
                                <div class="col-lg-8">
                                    <input type="text" name="publish_date" id="publish_date"
                                           class="form-control form-control-lg form-control-solid"
                                           placeholder="Select publish date"
                                           value="{{ old('publish_date', isset($event->publish_date) ? $event->publish_date->format('d M Y') : '') }}"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Description</label>
                                <div class="col-lg-8">
                                    <textarea name="description" rows="10" id="editor"
                                              class="form-control form-control-lg form-control-solid @error('description') is-invalid @enderror"
                                              placeholder="Enter Description">{{ old('description', $event->description ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Start Datetime</label>
                                <div class="col-lg-8">
                                    <input type="text" name="start_time" id="start_time"
                                           class="form-control form-control-lg form-control-solid"
                                           placeholder="Select start date & time"
                                           value="{{ old('start_time', isset($event->start_time) ? $event->start_time->format('d M Y H:i') : '') }}"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">End Datetime</label>
                                <div class="col-lg-8">
                                    <input type="text" name="end_time" id="end_time"
                                           class="form-control form-control-lg form-control-solid"
                                           placeholder="Select end date & time"
                                           value="{{ old('end_time', isset($event->end_time) ? $event->end_time->format('d M Y H:i') : '') }}"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Is Log Attendance?</label>
                                <div class="col-lg-8 d-flex align-items-center">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox"
                                               name="is_log_attendance" id="is_log_attendance"
                                               value="1" {{ old('is_log_attendance', $event->is_log_attendance ?? false) ? 'checked' : '' }}/>
                                        <label class="form-check-label" for="is_log_attendance">
                                            Enable user attendance tracking
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="attendance-date-fields" style="display:none;">
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Active From
                                        Date</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="active_user_from" id="active_user_from"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Active from"
                                               value="{{ old('active_user_from', isset($event->active_user_from) ? $event->active_user_from->format('d M Y H:i') : '') }}"/>
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Active To Date</label>
                                    <div class="col-lg-8">
                                        <input type="text" name="active_user_to" id="active_user_to"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Active to"
                                               value="{{ old('active_user_to', isset($event->active_user_to) ? $event->active_user_to->format('d M Y H:i') : '') }}"/>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{route('admin.events')}}" class="btn btn-light btn-active-light-primary me-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="kt_event_submit">
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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        "use strict";

        function setupImageUpload(inputId, previewImgId, placeholderId, filenameId) {
            const input = document.getElementById(inputId);
            const previewImg = document.getElementById(previewImgId);
            const placeholder = document.getElementById(placeholderId);
            const filenameEl = document.getElementById(filenameId);

            if (!input) return;

            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({icon: 'warning', title: 'File too large', text: 'Maximum file size is 5 MB.'});
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                    filenameEl.textContent = '✓ ' + file.name;
                };
                reader.readAsDataURL(file);
            });
        }

        KTUtil.onDOMContentLoaded(function () {

            flatpickr("#publish_date", {
                dateFormat: "d M Y",
                allowInput: true
            });

            ["#start_time", "#end_time", "#active_user_from", "#active_user_to"].forEach(id => {
                flatpickr(id, {
                    enableTime: true,
                    dateFormat: "d M Y H:i",
                    time_24hr: true,
                    onChange: function () {
                        validator.revalidateField('start_time');
                        validator.revalidateField('end_time');
                        validator.revalidateField('active_user_from');
                        validator.revalidateField('active_user_to');
                    }
                });
            });

            $('#player_type').select2({minimumResultsForSearch: Infinity});

            $('#domain').on('input', function () {
                $(this).val(
                    $(this).val()
                        .toLowerCase()
                        .replace(/\s+/g, '-')
                        .replace(/[^a-z0-9-]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '')
                );
            });

            const toggleAttendanceFields = () => {
                $('#attendance-date-fields').toggle($('#is_log_attendance').is(':checked'));
            };
            toggleAttendanceFields();
            $('#is_log_attendance').on('change', toggleAttendanceFields);

            const extractYouTubeData = (input) => {
                if (!input) return null;
                input = input.trim();
                if (/^[a-zA-Z0-9_-]{11}$/.test(input)) return {videoId: input};
                let videoId = null;
                try {
                    const url = new URL(input.includes('http') ? input : 'https://' + input);
                    videoId = url.searchParams.get('v');
                } catch (e) {
                }
                if (!videoId) {
                    const m = input.match(/(?:youtu\.be\/|youtube\.com\/.*v=)([a-zA-Z0-9_-]{11})/);
                    if (m) videoId = m[1];
                }
                return videoId ? {videoId} : null;
            };

            const extractVimeoID = (input) => {
                if (!input) return null;
                const m = input.match(/vimeo\.com\/(\d+)/);
                return m ? m[1] : (/^\d+$/.test(input) ? input : null);
            };

            let isManualEdit = false;

            const updateVideoPreview = (iframeCode) => {
                if (iframeCode) {
                    $('#preview-content').html(iframeCode).show();
                    $('#preview-placeholder').hide();
                } else {
                    $('#preview-content').hide();
                    $('#preview-placeholder').show();
                }
            };

            const generateEmbedContent = () => {
                if (isManualEdit) return;
                const input = $('#player_id').val()?.trim();
                const type = $('#player_type').val();
                if (!input || !type) {
                    $('#player_iframe').val('');
                    updateVideoPreview('');
                    return;
                }
                let iframe = '';
                if (type === 'youtube') {
                    const data = extractYouTubeData(input);
                    if (!data) return;
                    iframe = `<iframe width="100%" height="315" src="https://www.youtube.com/embed/${data.videoId}" allowfullscreen></iframe>`;
                } else if (type === 'vimeo') {
                    const id = extractVimeoID(input);
                    if (!id) return;
                    iframe = `<iframe width="100%" height="315" src="https://player.vimeo.com/video/${id}" allowfullscreen></iframe>`;
                } else if (type === 'other') {
                    iframe = `<iframe width="100%" height="315" src="${input}" allowfullscreen></iframe>`;
                }
                $('#player_iframe').val(iframe);
                updateVideoPreview(iframe);
            };

            $('#player_id, #player_type').on('input change', () => {
                isManualEdit = false;
                generateEmbedContent();
            });

            $('#player_iframe').on('input', function () {
                isManualEdit = true;
                updateVideoPreview($(this).val());
            });

            if ($('#player_iframe').val()) {
                updateVideoPreview($('#player_iframe').val());
            }

            setupImageUpload('favicon_input', 'favicon-preview-img', 'favicon-preview-placeholder', 'favicon-filename');
            setupImageUpload('logo_input', 'logo-preview-img', 'logo-preview-placeholder', 'logo-filename');

            const form = document.getElementById('kt_event_form');
            const submitBtn = document.getElementById('kt_event_submit');
            const isEdit = {{ isset($event->id) ? 'true' : 'false' }};

            const validator = FormValidation.formValidation(form, {
                fields: {

                    domain: {
                        validators: {
                            notEmpty: {message: 'Domain is required'},
                            regexp: {
                                regexp: /^[a-z0-9]+(?:-[a-z0-9]+)*$/,
                                message: 'Only lowercase letters, numbers and hyphens allowed'
                            },
                            stringLength: {
                                min: 3, max: 50,
                                message: 'Domain must be between 3 and 50 characters'
                            }
                        }
                    },

                    name: {
                        validators: {
                            notEmpty: {message: 'Name is required'}
                        }
                    },

                    email: {
                        validators: {
                            notEmpty: {message: 'Email is required'},
                            emailAddress: {message: 'Enter a valid email address'}
                        }
                    },

                    phone: {
                        validators: {
                            notEmpty: {message: 'Phone is required'}
                        }
                    },

                    favicon: {
                        validators: {
                            callback: {
                                message: 'Favicon is required',
                                callback: () => isEdit || document.getElementById('favicon_input').files.length > 0
                            },
                            file: {
                                extension: 'jpg,jpeg,png,webp',
                                type: 'image/jpeg,image/png,image/webp',
                                maxSize: 5 * 1024 * 1024,
                                message: 'Invalid file. Use JPG/PNG/WEBP up to 5 MB'
                            }
                        }
                    },

                    logo: {
                        validators: {
                            callback: {
                                message: 'Logo is required',
                                callback: () => isEdit || document.getElementById('logo_input').files.length > 0
                            },
                            file: {
                                extension: 'jpg,jpeg,png,webp',
                                type: 'image/jpeg,image/png,image/webp',
                                maxSize: 5 * 1024 * 1024,
                                message: 'Invalid file. Use JPG/PNG/WEBP up to 5 MB'
                            }
                        }
                    },

                    player_type: {
                        validators: {
                            notEmpty: {message: 'Player type is required'}
                        }
                    },

                    player_id: {
                        validators: {
                            notEmpty: {message: 'Player ID is required'}
                        }
                    },

                    player_iframe: {
                        validators: {
                            notEmpty: {message: 'Video iframe code is required'}
                        }
                    },

                    publish_date: {
                        validators: {
                            notEmpty: {message: 'Publish date is required'}
                        }
                    },

                    start_time: {
                        validators: {
                            notEmpty: {message: 'Start time is required'}
                        }
                    },

                    end_time: {
                        validators: {
                            notEmpty: {message: 'End time is required'},
                            callback: {
                                message: 'End time must be after start time',
                                callback: function (input) {
                                    const start = $('#start_time').val();
                                    const end = input.value;
                                    if (!end) return false;
                                    if (start && new Date(end) <= new Date(start)) {
                                        return {valid: false, message: 'End time must be after start time'};
                                    }
                                    return true;
                                }
                            }
                        }
                    },

                    active_user_from: {
                        validators: {
                            callback: {
                                message: 'Active from date is required',
                                callback: function () {
                                    if (!$('#is_log_attendance').is(':checked')) return true;
                                    return $('#active_user_from').val() !== '';
                                }
                            }
                        }
                    },

                    active_user_to: {
                        validators: {
                            callback: {
                                message: 'Active to date is required',
                                callback: function (input) {
                                    if (!$('#is_log_attendance').is(':checked')) return true;
                                    const from = $('#active_user_from').val();
                                    const to = input.value;
                                    if (!to) return {valid: false, message: 'Active to date is required'};
                                    if (from && new Date(to) <= new Date(from)) {
                                        return {valid: false, message: 'Must be after "Active From" date'};
                                    }
                                    return true;
                                }
                            }
                        }
                    }
                },

                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({rowSelector: '.row'})
                }
            });

            $('#is_log_attendance').on('change', () => {
                validator.revalidateField('active_user_from');
                validator.revalidateField('active_user_to');
            });

            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                validator.validate().then(function (status) {
                    if (status !== 'Valid') return;
                    submitBtn.setAttribute('data-kt-indicator', 'on');
                    submitBtn.disabled = true;
                    form.submit();
                });
            });

        });
        let editorInstance;
        ClassicEditor
            .create(document.querySelector('#editor'))
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

    </script>
@endpush
