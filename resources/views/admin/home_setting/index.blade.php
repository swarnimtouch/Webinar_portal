@extends('layouts.admin')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title fs-3 fw-bolder">{{$title}}</div>
                    </div>
                    <div class="card-body border-top p-9">
                        <form method="POST" action="{{ route('admin.home_setting.save') }}" id="kt_home_setting_form">
                            @csrf

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Title</label>
                                <div class="col-lg-8">
                                    <input type="text" name="title" id="title"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('title', $home_setting->title ?? '') }}"
                                           placeholder="Enter title "/>
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
                                            value="youtube" {{ old('player_type', $home_setting->player_type ?? '') === 'youtube' ? 'selected' : '' }}>
                                            YouTube
                                        </option>
                                        <option
                                            value="vimeo" {{ old('player_type', $home_setting->player_type ?? '') === 'vimeo' ? 'selected' : '' }}>
                                            Vimeo
                                        </option>
                                        <option
                                            value="other" {{ old('player_type', $home_setting->player_type ?? '') === 'other' ? 'selected' : '' }}>
                                            Other
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Player ID</label>
                                <div class="col-lg-8">
                                    <input type="text" name="player_id" id="player_id"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('player_id', $home_setting->player_id ?? '') }}"
                                           placeholder="Enter YouTube video ID"/>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">
                                    Video Iframe Code
                                </label>
                                <div class="col-lg-8">
                                    <textarea name="url" id="url"
                                              class="form-control form-control-lg form-control-solid"
                                              rows="5"
                                              placeholder="Auto-generated iframe code (You can also edit manually)">{{ old('url', $home_setting->url ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Video Preview Section -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Video Preview</label>
                                <div class="col-lg-8">
                                    <div id="video-preview" class="border rounded p-5 bg-light-primary"
                                         style="min-height: 200px;">
                                        <div class="text-center text-muted" id="preview-placeholder">
                                            <i class="bi bi-play-circle fs-3x mb-3"></i>
                                            <p class="mb-0">Video preview will appear here</p>
                                        </div>
                                        <div id="preview-content" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Publish Date</label>
                                <div class="col-lg-8">
                                    <input type="text"
                                           name="publish_date"
                                           id="publish_date"
                                           class="form-control form-control-lg form-control-solid"
                                           placeholder="Select publish date"
                                           value="{{ old('publish_date', isset($home_setting->publish_date) ? $home_setting->publish_date->format('d M Y') : '') }}"/>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">About Us</label>
                                <div class="col-lg-8">
                                    <textarea name="about_us"
                                              rows="10"
                                              id="editor"
                                              class="form-control form-control-lg form-control-solid @error('about_us') is-invalid @enderror"
                                              placeholder="Enter about_us">{{ old('about_us', $home_setting->about_us) }}</textarea>

                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">User Attendance</label>
                                <div class="col-lg-8">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="user_attendance"
                                               id="user_attendance"
                                               value="1" {{ old('user_attendance', $home_setting->user_attendance ?? false) ? 'checked' : '' }}/>
                                        <label class="form-check-label" for="user_attendance">
                                            Enable user attendance tracking
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Conditional Attendance Date Fields -->
                            <div id="attendance-date-fields" style="display: none;">
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label fw-bold fs-6">Event Start Time</label>
                                    <div class="col-lg-8">
                                        <input type="text"
                                               name="event_start_time"
                                               id="event_start_time"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Select start date & time"
                                               value="{{ old('event_start_time', isset($home_setting->event_start_time) ? $home_setting->event_start_time->format('d M Y H:i') : '') }}"/>
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label fw-bold fs-6">Event End Time</label>
                                    <div class="col-lg-8">
                                        <input type="text"
                                               name="event_end_time"
                                               id="event_end_time"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Select end date & time"
                                               value="{{ old('event_end_time', isset($home_setting->event_end_time) ? $home_setting->event_end_time->format('d M Y H:i') : '') }}"/>
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label fw-bold fs-6">Active From Date</label>
                                    <div class="col-lg-8">
                                        <input type="text"
                                               name="active_from_date"
                                               id="active_from_date"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Active from"
                                               value="{{ old('active_from_date', isset($home_setting->active_from_date) ? $home_setting->active_from_date->format('d M Y H:i') : '') }}"/>
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label fw-bold fs-6">Active To Date</label>
                                    <div class="col-lg-8">
                                        <input type="text"
                                               name="active_to_date"
                                               id="active_to_date"
                                               class="form-control form-control-lg form-control-solid"
                                               placeholder="Active to"
                                               value="{{ old('active_to_date', isset($home_setting->active_to_date) ? $home_setting->active_to_date->format('d M Y H:i') : '') }}"/>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="submit" class="btn btn-primary" id="kt_home_setting_submit">
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

        @if(session('success'))
        toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
        toastr.error("{{ session('error') }}");
        @endif

        let editorInstance;

        ClassicEditor
            .create(document.querySelector('#editor'))
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        KTUtil.onDOMContentLoaded(function () {


            flatpickr("#publish_date", {
                dateFormat: "d M Y",
                allowInput: true
            });

            flatpickr("#event_start_time", {
                enableTime: true,
                dateFormat: "d M Y H:i",
                time_24hr: true
            });

            flatpickr("#event_end_time", {
                enableTime: true,
                dateFormat: "d M Y H:i",
                time_24hr: true
            });

            flatpickr("#active_from_date", {
                enableTime: true,
                dateFormat: "d M Y H:i",
                time_24hr: true
            });

            flatpickr("#active_to_date", {
                enableTime: true,
                dateFormat: "d M Y H:i",
                time_24hr: true
            });


            $('#player_type').select2({
                minimumResultsForSearch: Infinity
            });


            const toggleAttendanceFields = () => {
                const isChecked = $('#user_attendance').is(':checked');
                const attendanceFields = $('#attendance-date-fields');

                if (isChecked) {
                    attendanceFields.slideDown(300);
                } else {
                    attendanceFields.slideUp(300);
                }
            };

            toggleAttendanceFields();
            $('#user_attendance').on('change', toggleAttendanceFields);


            const extractYouTubeData = (input) => {
                if (!input) return null;
                input = input.trim();

                if (/^[a-zA-Z0-9_-]{11}$/.test(input)) {
                    return {videoId: input, si: null};
                }

                let videoId = null;
                let si = null;

                try {
                    const url = new URL(input.includes('http') ? input : 'https://' + input);
                    const vParam = url.searchParams.get('v');
                    if (vParam && /^[a-zA-Z0-9_-]{11}$/.test(vParam)) {
                        videoId = vParam;
                    }
                    si = url.searchParams.get('si');
                } catch (e) {
                }

                if (!videoId) {
                    const patterns = [
                        /(?:youtube\.com\/watch\?.*v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/,
                        /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/,
                        /youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/
                    ];

                    for (let pattern of patterns) {
                        const match = input.match(pattern);
                        if (match && match[1]) {
                            videoId = match[1];
                            break;
                        }
                    }

                    const siMatch = input.match(/[?&]si=([a-zA-Z0-9_-]+)/);
                    if (siMatch && siMatch[1]) {
                        si = siMatch[1];
                    }
                }

                return videoId ? {videoId, si} : null;
            };

            const extractVimeoID = (input) => {
                if (!input) return null;
                input = input.trim();

                if (/^\d+$/.test(input)) return input;

                const patterns = [
                    /vimeo\.com\/(\d+)/,
                    /player\.vimeo\.com\/video\/(\d+)/
                ];

                for (let pattern of patterns) {
                    const match = input.match(pattern);
                    if (match && match[1]) return match[1];
                }

                return null;
            };


            let isManualEdit = false;

            const updateVideoPreview = (iframeCode) => {
                const previewContent = $('#preview-content');
                const previewPlaceholder = $('#preview-placeholder');

                if (iframeCode && iframeCode.trim() !== '') {
                    previewContent.html(iframeCode);
                    previewContent.find('iframe').css({
                        'width': '100%',
                        'max-width': '100%',
                        'height': 'auto',
                        'aspect-ratio': '16/9'
                    });
                    previewPlaceholder.hide();
                    previewContent.show();
                } else {
                    previewContent.hide();
                    previewPlaceholder.show();
                }
            };

            const generateEmbedContent = () => {
                if (isManualEdit) return;

                const playerInput = $('#player_id').val()?.trim();
                const playerType = $('#player_type').val();

                if (!playerInput || !playerType) {
                    if (!isManualEdit) {
                        $('#url').val('');
                        updateVideoPreview('');
                    }
                    return;
                }

                let iframeCode = '';
                let videoId = '';

                if (playerType === 'youtube') {
                    const youtubeData = extractYouTubeData(playerInput);
                    if (!youtubeData || !youtubeData.videoId) {
                        if (!isManualEdit) {
                            $('#url').val('');
                            updateVideoPreview('');
                        }
                        return;
                    }
                    videoId = youtubeData.videoId;
                    const siParam = youtubeData.si;
                    const embedUrl = siParam
                        ? `https://www.youtube.com/embed/${videoId}?si=${siParam}`
                        : `https://www.youtube.com/embed/${videoId}`;
                    iframeCode = `<iframe width="560" height="315" src="${embedUrl}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>`;

                } else if (playerType === 'vimeo') {
                    videoId = extractVimeoID(playerInput);
                    if (!videoId) {
                        if (!isManualEdit) {
                            $('#url').val('');
                            updateVideoPreview('');
                        }
                        return;
                    }
                    iframeCode = `<iframe src="https://player.vimeo.com/video/${videoId}?badge=0&autopause=0&player_id=0&app_id=58479" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write" title="Vimeo video player" allowfullscreen></iframe>`;

                } else if (playerType === 'other') {
                    iframeCode = `<iframe width="560" height="315" src="${playerInput}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                }

                $('#url').val(iframeCode);
                updateVideoPreview(iframeCode);
            };


            $('#player_id').on('input', function () {
                isManualEdit = false;
                generateEmbedContent();
            });

            $('#player_type').on('change', function () {
                isManualEdit = false;
                generateEmbedContent();
            });

            $('#url').on('input', function () {
                const currentVal = $(this).val();
                if (currentVal && currentVal.length > 0) {
                    isManualEdit = true;
                    updateVideoPreview(currentVal);
                }
            });

            $('#url').on('blur', function () {
                if (!$(this).val()) {
                    isManualEdit = false;
                }
            });

            setTimeout(() => {
                const urlVal = $('#url').val();
                if (!urlVal || urlVal.trim() === '') {
                    isManualEdit = false;
                    generateEmbedContent();
                } else {
                    isManualEdit = true;
                    updateVideoPreview(urlVal);
                }
            }, 500);


            const form = document.getElementById('kt_home_setting_form');
            const submitBtn = document.getElementById('kt_home_setting_submit');

            if (!form) return;

            const validator = FormValidation.formValidation(form, {
                fields: {
                    player_type: {
                        validators: {
                            notEmpty: {message: 'Player type is required'}
                        }
                    },
                    url: {
                        validators: {
                            notEmpty: {message: 'Video iframe code is required'}
                        }
                    },
                    player_id: {
                        validators: {
                            notEmpty: {message: 'Player ID or URL is required'}
                        }
                    },
                    publish_date: {
                        validators: {
                            notEmpty: {message: 'Publish date is required'}
                        }
                    },
                    event_start_time: {
                        validators: {
                            callback: {
                                message: 'Event start time is required when attendance is enabled',
                                callback: function (input) {
                                    const isAttendanceEnabled = $('#user_attendance').is(':checked');
                                    if (!isAttendanceEnabled) return true;
                                    return input.value !== '';
                                }
                            }
                        }
                    },
                    event_end_time: {
                        validators: {
                            callback: {
                                message: 'Event end time must be after start time',
                                callback: function (input) {
                                    const isAttendanceEnabled = $('#user_attendance').is(':checked');
                                    if (!isAttendanceEnabled) return true;
                                    const start = $('#event_start_time').val();
                                    if (!start || !input.value) return true;
                                    return new Date(input.value) >= new Date(start);
                                }
                            }
                        }
                    },
                    active_from_date: {
                        validators: {
                            callback: {
                                message: 'Active from date is required when attendance is enabled',
                                callback: function (input) {
                                    const isAttendanceEnabled = $('#user_attendance').is(':checked');
                                    if (!isAttendanceEnabled) return true;
                                    return input.value !== '';
                                }
                            }
                        }
                    },
                    active_to_date: {
                        validators: {
                            callback: {
                                message: 'Active to date must be after from date',
                                callback: function (input) {
                                    const isAttendanceEnabled = $('#user_attendance').is(':checked');
                                    if (!isAttendanceEnabled) return true;
                                    const from = $('#active_from_date').val();
                                    if (!from || !input.value) return true;
                                    return new Date(input.value) >= new Date(from);
                                }
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


            $('#event_start_time').on('change', () => {
                validator.revalidateField('event_end_time');
            });

            $('#active_from_date').on('change', () => {
                validator.revalidateField('active_to_date');
            });

            $('#user_attendance').on('change', () => {
                validator.revalidateField('event_start_time');
                validator.revalidateField('event_end_time');
                validator.revalidateField('active_from_date');
                validator.revalidateField('active_to_date');
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


    </script>
@endpush
