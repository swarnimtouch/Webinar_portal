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
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Company</label>
                                <div class="col-lg-8">
                                    <select name="company_id" id="company_id"
                                            class="form-select form-select-lg form-select-solid"
                                            data-placeholder="Search or add a company">
                                        @if($selectedCompany)
                                            <option value="{{ old('company_id', $event->company_id) }}"
                                                    data-slug="{{ $selectedCompany->slug }}" selected>
                                                {{ $selectedCompany->name }}
                                            </option>
                                        @endif
                                    </select>
                                    <div class="text-muted fs-7 mt-2" id="event_url_preview">
                                        @if($event->exists && $event->slug)
                                            Live URL: https://{{ config('app.event_live_subdomain', 'live') }}.{{ config('app.event_base_domain') }}/{{ $event->slug }}
                                        @else
                                            Enter the event name to preview the live URL.
                                        @endif
                                    </div>
                                    @if($event->exists)
                                        <div class="text-muted fs-7 mt-1">Event slug is locked: <strong>{{ $event->slug }}</strong></div>
                                    @endif
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

                            @php
                                $agendaItems = old('session_agenda', $event->session_agenda ?? []);
                                if (empty($agendaItems)) $agendaItems = [[]];
                            @endphp
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Session Agenda</label>
                                <div class="col-lg-8">
                                    <div id="session-agenda-list">
                                        @foreach($agendaItems as $agendaIndex => $agenda)
                                            <div class="agenda-input-row border rounded p-4 mb-4" data-index="{{ $agendaIndex }}">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <input type="text" name="session_agenda[{{ $agendaIndex }}][time]"
                                                               value="{{ $agenda['time'] ?? '' }}" class="form-control form-control-solid"
                                                               placeholder="Time, e.g. 2:00 PM">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" name="session_agenda[{{ $agendaIndex }}][duration]"
                                                               value="{{ $agenda['duration'] ?? '' }}" class="form-control form-control-solid"
                                                               placeholder="Duration, e.g. 30 min">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select name="session_agenda[{{ $agendaIndex }}][status]" class="form-select form-select-solid">
                                                            <option value="upcoming" @selected(($agenda['status'] ?? 'upcoming') === 'upcoming')>Upcoming</option>
                                                            <option value="live" @selected(($agenda['status'] ?? '') === 'live')>Live Now</option>
                                                            <option value="completed" @selected(($agenda['status'] ?? '') === 'completed')>Completed</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <input type="text" name="session_agenda[{{ $agendaIndex }}][title]"
                                                               value="{{ $agenda['title'] ?? '' }}" class="form-control form-control-solid"
                                                               maxlength="255" placeholder="Agenda title">
                                                    </div>
                                                    <div class="col-12 d-flex gap-3">
                                                        <input type="text" name="session_agenda[{{ $agendaIndex }}][description]"
                                                               value="{{ $agenda['description'] ?? '' }}" class="form-control form-control-solid"
                                                               maxlength="1000" placeholder="Agenda description">
                                                        <button type="button" class="btn btn-icon btn-light-danger remove-agenda-btn" title="Remove">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" id="add-agenda-btn" class="btn btn-light-primary btn-sm">
                                        <i class="bi bi-plus-lg"></i> Add Agenda Item
                                    </button>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Event Resources</label>
                                <div class="col-lg-8">
                                    <div id="event-resources-list">
                                        @foreach($eventResources->isNotEmpty() ? $eventResources : collect([null]) as $index => $savedResource)
                                            <div class="resource-input-row border rounded p-5 mb-4">
                                                <input type="hidden" class="resource-id"
                                                       name="resource_id[{{ $index }}]"
                                                       value="{{ $savedResource?->id }}">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="flex-grow-1">
                                                        <input type="text"
                                                               name="resource_title[{{ $index }}]"
                                                               class="form-control form-control-lg form-control-solid mb-3 resource-title"
                                                               maxlength="255"
                                                               value="{{ old('resource_title.'.$index, $savedResource?->title) }}"
                                                               placeholder="Resource title">
                                                        <input type="file"
                                                               name="resource_file[{{ $index }}]"
                                                               class="form-control form-control-lg form-control-solid resource-file"
                                                               accept=".pdf,application/pdf">
                                                        <div class="text-muted fs-7 mt-2 resource-file-note">
                                                            @if($savedResource)
                                                                Current file: {{ $savedResource->original_name }} &middot;
                                                            @endif
                                                            PDF only, maximum 10 MB.
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                            class="btn btn-icon btn-light-danger remove-resource-btn"
                                                            @if($index === 0) style="display:none" @endif
                                                            title="Remove resource">
                                                        <i class="bi bi-trash fs-3"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-light-primary" id="add-resource-btn">
                                        <i class="bi bi-plus-lg"></i> Add Resource
                                    </button>
                                </div>
                            </div>

                            <template id="resource-row-template">
                                <div class="resource-input-row border rounded p-5 mb-4">
                                    <input type="hidden" class="resource-id" name="resource_id[__INDEX__]" value="">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="flex-grow-1">
                                            <input type="text" name="resource_title[__INDEX__]"
                                                   class="form-control form-control-lg form-control-solid mb-3 resource-title"
                                                   maxlength="255" placeholder="Resource title">
                                            <input type="file" name="resource_file[__INDEX__]"
                                                   class="form-control form-control-lg form-control-solid resource-file"
                                                   accept=".pdf,application/pdf">
                                            <div class="text-muted fs-7 mt-2 resource-file-note">PDF only, maximum 10 MB.</div>
                                        </div>
                                        <button type="button" class="btn btn-icon btn-light-danger remove-resource-btn"
                                                title="Remove resource">
                                            <i class="bi bi-trash fs-3"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>

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

    <div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold mb-0">Add Company</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-8">
                    <div class="mb-5">
                        <label class="form-label required fw-bold">Company Name</label>
                        <input type="text" id="new_company_name" class="form-control form-control-solid"
                               maxlength="255" autocomplete="organization">
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-bold">Email</label>
                        <input type="email" id="new_company_email" class="form-control form-control-solid"
                               maxlength="255" placeholder="company@example.com" autocomplete="email">
                    </div>
                    <div>
                        <label class="form-label required fw-bold">Phone Number</label>
                        <input type="text" id="new_company_phone" class="form-control form-control-solid"
                               maxlength="30" placeholder="Enter phone number" autocomplete="tel">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save_company_btn">
                        <span class="indicator-label">Add Company</span>
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
            let validator = null;

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
                        validator?.revalidateField('start_time');
                        validator?.revalidateField('end_time');
                        validator?.revalidateField('active_user_from');
                        validator?.revalidateField('active_user_to');
                    }
                });
            });

            $('#player_type').select2({minimumResultsForSearch: Infinity});

            const companySearchUrl = @json(route('admin.events.companies.search'));
            const companyStoreUrl = @json(route('admin.events.companies.store'));
            const baseDomain = @json(config('app.event_base_domain', 'doctorly.in'));
            const liveSubdomain = @json(config('app.event_live_subdomain', 'live'));
            const lockedEventSlug = @json($event->exists ? $event->slug : null);
            let currentCompanySearch = '';

            const slugify = value => value.toLowerCase().trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');

            const updateEventUrlPreview = () => {
                const eventSlug = lockedEventSlug || slugify($('#name').val() || '');
                const preview = document.getElementById('event_url_preview');
                const liveUrl = eventSlug
                    ? `https://${liveSubdomain}.${baseDomain}/${eventSlug}`
                    : '';
                preview.textContent = liveUrl
                    ? `Live URL: ${liveUrl}`
                    : 'Enter the event name to preview the live URL.';
            };

            const companySelect = $('#company_id').select2({
                placeholder: 'Search or add a company',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: companySearchUrl,
                    dataType: 'json',
                    delay: 250,
                    data: params => {
                        currentCompanySearch = (params.term || '').trim();
                        return {search: currentCompanySearch};
                    },
                    processResults: data => {
                        const results = (data.companies || []).map(company => ({
                            id: company.id,
                            text: company.name,
                            slug: company.slug,
                        }));

                        if (results.length === 0 && currentCompanySearch) {
                            results.push({
                                id: '__create_company__',
                                text: `+ Add "${currentCompanySearch}"`,
                                companyName: currentCompanySearch,
                                isNewCompany: true,
                            });
                        }

                        return {results};
                    },
                    cache: true,
                },
                templateResult: company => {
                    if (company.isNewCompany) {
                        return $(`<div class="text-primary fw-bold py-1"></div>`).text(company.text);
                    }
                    return company.text;
                },
            });

            companySelect.on('select2:select', function (event) {
                if (event.params.data.isNewCompany) {
                    const name = event.params.data.companyName;
                    $(this).find('option[value="__create_company__"]').remove();
                    $(this).val(null).trigger('change.select2');
                    $('#new_company_name').val(name);
                    $('#new_company_email, #new_company_phone').val('');
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('addCompanyModal')).show();
                    return;
                }

                updateEventUrlPreview();
            }).on('select2:clear', function () {
                updateEventUrlPreview();
            });

            $('#addCompanyModal').on('shown.bs.modal', function () {
                $('#new_company_email').trigger('focus');
            });

            $('#save_company_btn').on('click', function () {
                const button = this;
                const name = $('#new_company_name').val().trim();
                const email = $('#new_company_email').val().trim();
                const phone = $('#new_company_phone').val().trim();

                if (!name || !email || !phone) {
                    toastr.warning('Company name, email and phone number are required.');
                    return;
                }

                button.setAttribute('data-kt-indicator', 'on');
                button.disabled = true;

                fetch(companyStoreUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({name, email, phone})
                })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        const company = data.company;
                        const option = new Option(company.name, company.id, true, true);
                        option.dataset.slug = company.slug;
                        companySelect.append(option).trigger('change');
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('addCompanyModal')).hide();
                        updateEventUrlPreview();
                        toastr.success('Company added successfully.');
                    })
                    .catch(error => {
                        const validationMessage = error.errors
                            ? Object.values(error.errors).flat()[0]
                            : null;
                        toastr.error(validationMessage || error.message || 'Unable to add company.');
                    })
                    .finally(() => {
                        button.removeAttribute('data-kt-indicator');
                        button.disabled = false;
                    });
            });

            $('#name').on('input', updateEventUrlPreview);
            updateEventUrlPreview();

            const resourcesList = document.getElementById('event-resources-list');
            const resourceTemplate = document.getElementById('resource-row-template');

            const reindexResources = () => {
                resourcesList.querySelectorAll('.resource-input-row').forEach((row, index) => {
                    row.querySelector('.resource-id').name = `resource_id[${index}]`;
                    row.querySelector('.resource-title').name = `resource_title[${index}]`;
                    row.querySelector('.resource-file').name = `resource_file[${index}]`;
                    row.querySelector('.remove-resource-btn').style.display = index === 0 ? 'none' : '';
                });
            };

            document.getElementById('add-resource-btn').addEventListener('click', () => {
                const index = resourcesList.querySelectorAll('.resource-input-row').length;
                resourcesList.insertAdjacentHTML(
                    'beforeend',
                    resourceTemplate.innerHTML.replaceAll('__INDEX__', index)
                );
                reindexResources();
            });

            resourcesList.addEventListener('click', event => {
                const removeButton = event.target.closest('.remove-resource-btn');
                if (!removeButton) return;

                const row = removeButton.closest('.resource-input-row');
                const rows = resourcesList.querySelectorAll('.resource-input-row');

                if (rows.length === 1) {
                    row.querySelector('.resource-id').value = '';
                    row.querySelector('.resource-title').value = '';
                    row.querySelector('.resource-file').value = '';
                    row.querySelector('.resource-file-note').textContent = 'PDF only, maximum 10 MB.';
                } else {
                    row.remove();
                    reindexResources();
                }
            });

            reindexResources();

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

            validator = FormValidation.formValidation(form, {
                fields: {

                    company_id: {
                        validators: {
                            notEmpty: {message: 'Company is required'}
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

            companySelect.on('change', () => validator.revalidateField('company_id'));

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
        let agendaIndex = {{ count($agendaItems) }};

        $('#add-agenda-btn').on('click', function () {
            const index = agendaIndex++;
            $('#session-agenda-list').append(`
                <div class="agenda-input-row border rounded p-4 mb-4" data-index="${index}">
                    <div class="row g-3">
                        <div class="col-md-4"><input type="text" name="session_agenda[${index}][time]" class="form-control form-control-solid" placeholder="Time, e.g. 2:00 PM"></div>
                        <div class="col-md-4"><input type="text" name="session_agenda[${index}][duration]" class="form-control form-control-solid" placeholder="Duration, e.g. 30 min"></div>
                        <div class="col-md-4"><select name="session_agenda[${index}][status]" class="form-select form-select-solid"><option value="upcoming">Upcoming</option><option value="live">Live Now</option><option value="completed">Completed</option></select></div>
                        <div class="col-12"><input type="text" name="session_agenda[${index}][title]" class="form-control form-control-solid" maxlength="255" placeholder="Agenda title"></div>
                        <div class="col-12 d-flex gap-3"><input type="text" name="session_agenda[${index}][description]" class="form-control form-control-solid" maxlength="1000" placeholder="Agenda description"><button type="button" class="btn btn-icon btn-light-danger remove-agenda-btn" title="Remove"><i class="bi bi-trash"></i></button></div>
                    </div>
                </div>`);
        });

        $(document).on('click', '.remove-agenda-btn', function () {
            $(this).closest('.agenda-input-row').remove();
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
