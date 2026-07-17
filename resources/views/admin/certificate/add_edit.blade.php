@extends('layouts.admin')
@push('styles')
    <style>
        @font-face { font-family: 'CertificatePoppins'; src: url('{{ asset('storage/certificates/fonts/Poppins-Bold.ttf') }}') format('truetype'); font-weight: 700; }
        .certificate-builder-form { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(460px, .95fr); column-gap: 2.5rem; align-items: start; }
        .certificate-builder-form > .row, .certificate-builder-form > .separator { grid-column: 1; }
        .certificate-preview-column { grid-column: 2 !important; grid-row: 1 / span 20; position: sticky; top: 90px; margin: 0 !important; padding: 1.5rem; background: #f8f9fb; border: 1px solid #e4e6ef; border-radius: 1rem; }
        .certificate-preview-column > label, .certificate-preview-column > div { width: 100%; max-width: none; }
        .certificate-preview-column > label { display: none; }
        .certificate-preview-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
        .certificate-preview-heading .preview-icon { display: grid; place-items: center; width: 42px; height: 42px; flex: 0 0 42px; color: #009ef7; background: #e8f6ff; border-radius: .75rem; }
        .certificate-preview-stage { position: relative; width: 100%; aspect-ratio: 3 / 2; overflow: hidden; background-color: #fff; background-image: linear-gradient(45deg, #f5f7fa 25%, transparent 25%), linear-gradient(-45deg, #f5f7fa 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f5f7fa 75%), linear-gradient(-45deg, transparent 75%, #f5f7fa 75%); background-size: 24px 24px; background-position: 0 0, 0 12px, 12px -12px, -12px 0; border: 1px solid #d8dbe2; border-radius: .85rem; box-shadow: 0 8px 24px rgba(31, 41, 55, .08); }
        .certificate-preview-stage img { width: 100%; height: 100%; object-fit: contain; pointer-events: none; }
        .certificate-preview-field { position: absolute; transform: translate(-50%, -50%); white-space: nowrap; cursor: grab; user-select: none; touch-action: none; font-family: 'CertificatePoppins', Poppins, sans-serif; line-height: 1; z-index: 3; }
        .certificate-preview-field:active { cursor: grabbing; }
        .certificate-preview-modal { position: fixed; inset: 0; z-index: 1100; background: rgba(0,0,0,.58); padding: 2vh 5vw; overflow: auto; }
        .certificate-preview-modal-card { max-width: 780px; max-height: 96vh; overflow: auto; margin: auto; background: #fff; border-radius: .85rem; padding: 1.25rem; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
        .empty-position-box { display: flex; align-items: center; justify-content: center; min-width: 180px; min-height: 42px; padding: .55rem 1rem; color: #7c2d12 !important; background: rgba(255, 247, 237, .92); border: 2px dashed #f59e0b; border-radius: .5rem; box-shadow: 0 0 0 4px rgba(245, 158, 11, .12); }
        .empty-position-box::before, .empty-position-box::after { content: ''; position: absolute; background: rgba(245, 158, 11, .55); pointer-events: none; }
        .empty-position-box::before { width: 1px; height: 1000px; left: 50%; top: 50%; transform: translateY(-50%); }
        .empty-position-box::after { height: 1px; width: 1000px; left: 50%; top: 50%; transform: translateX(-50%); }
        .position-resize-handle { position: absolute; top: 50%; width: 14px; height: 34px; z-index: 5; transform: translateY(-50%); background: #fff; border: 2px solid #f59e0b; border-radius: 5px; cursor: ew-resize; box-shadow: 0 2px 6px rgba(0, 0, 0, .18); }
        .position-resize-handle::after { content: ''; position: absolute; inset: 7px 4px; border-left: 1px solid #f59e0b; border-right: 1px solid #f59e0b; }
        .position-resize-handle.start { left: -8px; }
        .position-resize-handle.end { right: -8px; }
        .preview-coordinate-bar { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
        .preview-coordinate-bar span { padding: .45rem .7rem; color: #5e6278; background: #fff; border: 1px solid #e4e6ef; border-radius: .5rem; font-size: .85rem; font-weight: 600; }
        #certificate_editor_wrap { display: block; }
        @media (max-width: 1199.98px) {
            .certificate-builder-form { grid-template-columns: 1fr; }
            .certificate-preview-column { grid-column: 1 !important; grid-row: auto; position: static; margin-top: 1rem !important; }
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
                              id="kt_certificate_form" class="certificate-builder-form"
                              enctype="multipart/form-data">

                            @csrf
                            @if($certificate->exists)
                                @method('PUT')
                            @endif
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
                                                    {{ old('event_id', $certificate->event_id ?? '') == $event->id ? 'selected' : '' }}>
                                                    {{ $event->name }}
                                                </option>
                                            @endforeach


                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="event_id" value="{{ auth()->user()->event_id }}">
                            @endif
                            <!-- Name -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Certificate
                                    Name</label>
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
                                            <img src="{{ \App\Support\EventStorage::url($certificate->background_image) }}"
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
                            <div class="row mb-6">
                                <label
                                    class="col-lg-4 col-form-label fw-bold fs-6">Font
                                    File</label>
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
                                           accept=".ttf,.otf"/>
                                    <div class="form-text">Default font file: <strong>Poppins-Bold.ttf</strong>. Upload a TTF/OTF file only to replace it.</div>
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
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Text Position
                                    (X)</label>
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
                                    <div class="form-text">Horizontal range where the name text will be rendered
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-bold fs-6">Text Position
                                    (Y)</label>
                                <div class="col-lg-8">
                                    <input type="number" name="y"
                                           class="form-control form-control-lg form-control-solid"
                                           value="{{ old('y', $certificate->y ?? 0) }}"
                                           min="0" placeholder="0"/>
                                    <div class="form-text">Vertical position where the name text will be rendered
                                    </div>
                                </div>
                            </div>

                            <div class="separator separator-dashed my-8"></div>
                            <div class="row mb-6 certificate-preview-column">
                                <label class="col-lg-4 col-form-label fw-bold fs-6">Certificate Preview</label>
                                <div class="col-lg-8">
                                    <div class="certificate-preview-heading">
                                        <div class="d-flex gap-3">
                                            <span class="preview-icon"><i class="bi bi-person-bounding-box fs-2"></i></span>
                                            <div>
                                                <h3 class="mb-1">Participant Name Placement</h3>
                                                <p class="text-muted mb-0">Drag the marked name area to position it on the certificate.</p>
                                            </div>
                                        </div>
                                        <span class="badge badge-light-primary">Live preview</span>
                                    </div>
                                    <div id="certificate_editor_wrap" class="mb-4">
                                        <div class="certificate-preview-stage" id="certificate_editor_stage">
                                            <img id="certificate_editor_image" alt="Certificate background">
                                            <div class="certificate-preview-field empty-position-box" id="certificate_editor_position">
                                                <span class="position-resize-handle start" id="position_resize_start" title="Drag to resize from the left"></span>
                                                Participant Name
                                                <span class="position-resize-handle end" id="position_resize_end" title="Drag to resize from the right"></span>
                                            </div>
                                        </div>
                                        <div class="preview-coordinate-bar">
                                            <span>Start X: <strong id="preview_start_x">0</strong></span>
                                            <span>End X: <strong id="preview_end_x">0</strong></span>
                                            <span>Y: <strong id="preview_y">0</strong></span>
                                        </div>
                                        <div class="form-text mt-2"><i class="bi bi-arrows-move me-1"></i>Drag the orange marker. The X and Y fields update automatically.</div>
                                    </div>
                                    <div id="certificate_preview_wrap" class="d-none certificate-preview-modal">
                                        <div class="certificate-preview-modal-card">
                                            <div class="d-flex justify-content-between align-items-center mb-4"><h3 class="mb-0">Generate Certificate Preview</h3><button type="button" class="btn btn-sm btn-light" id="certificate_preview_close">Close</button></div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Recipient Name</label>
                                                <div class="d-flex gap-2">
                                                    <input type="text" id="preview_name" class="form-control form-control-solid" value="Dr. Sample Name" placeholder="Recipient name">
                                                    <button type="button" class="btn btn-light-primary flex-shrink-0" id="render_preview_btn">Render Preview</button>
                                                </div>
                                            </div>
                                            <div class="certificate-preview-stage" id="certificate_preview_stage">
                                                <img id="certificate_preview_image" alt="Certificate background">
                                                <div class="certificate-preview-field" id="certificate_preview_name" data-preview-field="name">Sample Participant Name</div>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="button" class="btn btn-light" id="certificate_preview_close_bottom">Close</button>
                                                <button type="button" class="btn btn-primary" id="certificate_preview_download"><i class="bi bi-download me-1"></i>Download Preview</button>
                                            </div>
                                            <div class="form-text mt-3">Field select karke certificate par drag karein; position automatically update hogi.</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline btn-outline-primary w-100 mt-4" id="certificate_preview_btn"><i class="bi bi-eye me-1"></i>Generate Preview</button>
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

                const colorPicker = document.getElementById('font_color_picker');
                const colorText = document.getElementById('font_color');
                const isAdmin = {{ auth()->user()->type === 'admin' ? 'true' : 'false' }};
                const isEdit = {{ $certificate->exists ? 'true' : 'false' }};

                colorPicker.addEventListener('input', () => {
                    colorText.value = colorPicker.value;
                });

                colorText.addEventListener('input', () => {
                    const val = colorText.value;
                    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        colorPicker.value = val;
                    }
                });

                const previewBtn = document.getElementById('certificate_preview_btn');
                const previewWrap = document.getElementById('certificate_preview_wrap');
                const previewStage = document.getElementById('certificate_preview_stage');
                const previewImage = document.getElementById('certificate_preview_image');
                const previewText = document.getElementById('certificate_preview_name');
                const previewName = document.getElementById('preview_name');
                const backgroundInput = document.getElementById('background_image');
                const editorWrap = document.getElementById('certificate_editor_wrap');
                const editorStage = document.getElementById('certificate_editor_stage');
                const editorImage = document.getElementById('certificate_editor_image');
                const editorPosition = document.getElementById('certificate_editor_position');
                const resizeStart = document.getElementById('position_resize_start');
                const resizeEnd = document.getElementById('position_resize_end');
                const startXInput = form.querySelector('[name="start_x"]');
                const endXInput = form.querySelector('[name="end_x"]');
                const yInput = form.querySelector('[name="y"]');
                const fontSizeInput = form.querySelector('[name="font_size"]');
                const boldInput = document.getElementById('is_bold');
                const previewStartX = document.getElementById('preview_start_x');
                const previewEndX = document.getElementById('preview_end_x');
                const previewY = document.getElementById('preview_y');
                const currentBackground = @json($certificate->exists && $certificate->background_image ? \App\Support\EventStorage::url($certificate->background_image) : null);
                let logicalWidth = 1200;
                let logicalHeight = 800;

                const normalizeCoordinates = () => {
                    let startX = Math.max(0, Math.min(logicalWidth - 1, Number(startXInput.value) || 0));
                    let endX = Number(endXInput.value) || logicalWidth;
                    let y = Number(yInput.value);

                    if (endX <= startX || endX > logicalWidth) endX = logicalWidth;
                    if (!Number.isFinite(y) || y <= 0 || y > logicalHeight) y = Math.round(logicalHeight / 2);

                    startXInput.max = Math.round(logicalWidth - 1);
                    endXInput.max = Math.round(logicalWidth);
                    yInput.max = Math.round(logicalHeight);
                    startXInput.value = Math.round(startX);
                    endXInput.value = Math.round(endX);
                    yInput.value = Math.round(y);
                };

                const renderEditorPosition = () => {
                    const startX = Number(startXInput.value) || 0;
                    const configuredEndX = Number(endXInput.value) || 0;
                    const endX = configuredEndX > startX ? configuredEndX : logicalWidth;
                    const y = Number(yInput.value) || logicalHeight / 2;
                    editorPosition.style.left = `${((startX + endX) / 2 / logicalWidth) * 100}%`;
                    editorPosition.style.top = `${(y / logicalHeight) * 100}%`;
                    editorPosition.style.width = `${Math.max(100, ((endX - startX) / logicalWidth) * editorStage.clientWidth)}px`;
                    editorPosition.style.fontSize = `${Math.max(10, (Number(fontSizeInput.value) || 30) * (editorStage.clientWidth / logicalWidth))}px`;
                    editorPosition.style.fontWeight = boldInput.checked ? '700' : '600';
                    previewStartX.textContent = Math.round(startX);
                    previewEndX.textContent = Math.round(endX);
                    previewY.textContent = Math.round(y);
                };

                const setEditorBackground = source => {
                    if (!source) return;
                    editorWrap.style.display = 'block';
                    editorImage.onload = () => {
                        logicalWidth = editorImage.naturalWidth || 1200;
                        logicalHeight = editorImage.naturalHeight || 800;
                        editorStage.style.aspectRatio = `${logicalWidth} / ${logicalHeight}`;
                        normalizeCoordinates();
                        renderEditorPosition();
                    };
                    editorImage.src = source;
                };

                const renderPreviewText = () => {
                    const startX = Number(startXInput.value) || 0;
                    const configuredEndX = Number(endXInput.value) || 0;
                    const endX = configuredEndX > startX ? configuredEndX : logicalWidth;
                    const y = Number(yInput.value) || Math.round(logicalHeight / 2);
                    const scale = previewStage.clientWidth / logicalWidth;

                    previewText.textContent = previewText.dataset.showSample === 'true' ? (previewName.value || 'Sample Participant Name') : '';
                    previewText.classList.toggle('empty-position-box', previewText.dataset.showSample !== 'true');
                    previewText.style.left = `${((startX + endX) / 2 / logicalWidth) * 100}%`;
                    previewText.style.top = `${(y / logicalHeight) * 100}%`;
                    previewText.style.fontSize = `${Math.max(8, (Number(fontSizeInput.value) || 30) * scale)}px`;
                    previewText.style.color = colorText.value || '#000000';
                    previewText.style.fontWeight = document.getElementById('is_bold').checked ? '700' : '600';

                };

                const setPreviewBackground = source => {
                    if (!source) {
                        previewImage.removeAttribute('src');
                        logicalWidth = 1200;
                        logicalHeight = 800;
                        renderPreviewText();
                        return;
                    }
                    previewImage.onload = () => {
                        logicalWidth = previewImage.naturalWidth || 1200;
                        logicalHeight = previewImage.naturalHeight || 800;
                        previewStage.style.aspectRatio = `${logicalWidth} / ${logicalHeight}`;
                        renderPreviewText();
                    };
                    previewImage.src = source;
                };

                previewBtn.addEventListener('click', () => {
                    previewText.dataset.showSample = 'true';
                    previewWrap.classList.remove('d-none');
                    const file = backgroundInput.files[0];
                    setPreviewBackground(file ? URL.createObjectURL(file) : currentBackground);
                    renderPreviewText();
                    previewWrap.scrollIntoView({behavior: 'smooth', block: 'nearest'});
                });
                document.getElementById('render_preview_btn').addEventListener('click', renderPreviewText);
                document.getElementById('certificate_preview_close').addEventListener('click', () => previewWrap.classList.add('d-none'));
                document.getElementById('certificate_preview_close_bottom').addEventListener('click', () => previewWrap.classList.add('d-none'));
                previewWrap.addEventListener('click', event => {
                    if (event.target === previewWrap) previewWrap.classList.add('d-none');
                });

                [previewName, startXInput, endXInput, yInput, fontSizeInput, colorText, document.getElementById('is_bold')]
                    .forEach(input => input.addEventListener('input', renderPreviewText));

                previewText.addEventListener('pointerdown', event => {
                    event.preventDefault();
                    previewText.setPointerCapture(event.pointerId);
                });
                previewText.addEventListener('pointermove', event => {
                    if (!previewText.hasPointerCapture(event.pointerId)) return;
                    const rect = previewStage.getBoundingClientRect();
                    const centerX = Math.max(0, Math.min(logicalWidth, ((event.clientX - rect.left) / rect.width) * logicalWidth));
                    const centerY = Math.max(0, Math.min(logicalHeight, ((event.clientY - rect.top) / rect.height) * logicalHeight));
                    const rangeWidth = Math.max(1, (Number(endXInput.value) || logicalWidth) - (Number(startXInput.value) || 0));
                    startXInput.value = Math.max(0, Math.round(centerX - rangeWidth / 2));
                    endXInput.value = Math.min(logicalWidth, Math.round(centerX + rangeWidth / 2));
                    yInput.value = Math.round(centerY);
                    renderPreviewText();
                });

                backgroundInput.addEventListener('change', () => {
                    const file = backgroundInput.files[0];
                    if (!file) return;
                    setEditorBackground(URL.createObjectURL(file));
                });

                editorPosition.addEventListener('pointerdown', event => {
                    if (event.target.closest('.position-resize-handle')) return;
                    event.preventDefault(); editorPosition.setPointerCapture(event.pointerId);
                });
                editorPosition.addEventListener('pointermove', event => {
                    if (!editorPosition.hasPointerCapture(event.pointerId)) return;
                    const rect = editorStage.getBoundingClientRect();
                    const centerX = Math.max(0, Math.min(logicalWidth, ((event.clientX - rect.left) / rect.width) * logicalWidth));
                    const centerY = Math.max(0, Math.min(logicalHeight, ((event.clientY - rect.top) / rect.height) * logicalHeight));
                    const rangeWidth = Math.max(1, (Number(endXInput.value) || logicalWidth) - (Number(startXInput.value) || 0));
                    startXInput.value = Math.max(0, Math.round(centerX - rangeWidth / 2));
                    endXInput.value = Math.min(logicalWidth, Math.round(centerX + rangeWidth / 2));
                    yInput.value = Math.round(centerY);
                    renderEditorPosition();
                });

                const bindResizeHandle = (handle, edge) => {
                    handle.addEventListener('pointerdown', event => {
                        event.preventDefault();
                        event.stopPropagation();
                        handle.setPointerCapture(event.pointerId);
                    });

                    handle.addEventListener('pointermove', event => {
                        if (!handle.hasPointerCapture(event.pointerId)) return;

                        const rect = editorStage.getBoundingClientRect();
                        const x = Math.max(0, Math.min(
                            logicalWidth,
                            ((event.clientX - rect.left) / rect.width) * logicalWidth
                        ));

                        if (edge === 'start') {
                            const endX = Number(endXInput.value) || logicalWidth;
                            startXInput.value = Math.round(Math.min(x, endX - 1));
                        } else {
                            const startX = Number(startXInput.value) || 0;
                            endXInput.value = Math.round(Math.max(x, startX + 1));
                        }

                        renderEditorPosition();
                    });
                };

                bindResizeHandle(resizeStart, 'start');
                bindResizeHandle(resizeEnd, 'end');

                [startXInput, endXInput, yInput, fontSizeInput, boldInput]
                    .forEach(input => input.addEventListener('input', renderEditorPosition));
                if (currentBackground) {
                    setEditorBackground(currentBackground);
                } else {
                    renderEditorPosition();
                }

                document.getElementById('certificate_preview_download').addEventListener('click', () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = logicalWidth; canvas.height = logicalHeight;
                    const context = canvas.getContext('2d');
                    context.fillStyle = '#fff'; context.fillRect(0, 0, canvas.width, canvas.height);
                    if (previewImage.src) context.drawImage(previewImage, 0, 0, canvas.width, canvas.height);
                    const startX = Number(startXInput.value) || 0;
                    const endX = Number(endXInput.value) > startX ? Number(endXInput.value) : logicalWidth;
                    context.fillStyle = colorText.value || '#000';
                    context.font = `700 ${Number(fontSizeInput.value) || 30}px Poppins`;
                    context.textAlign = 'center'; context.textBaseline = 'middle';
                    context.fillText(previewName.value || 'Sample Participant Name', (startX + endX) / 2, Number(yInput.value) || logicalHeight / 2);
                    const link = document.createElement('a');
                    link.download = 'certificate-preview.png'; link.href = canvas.toDataURL('image/png'); link.click();
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
                        ...(isAdmin && {
                            event_id: {
                                validators: {
                                    notEmpty: {message: 'Event is required'}
                                }
                            }
                        }),
                        background_image: {
                            validators: {
                                callback: {
                                    callback: function () {
                                        const file = document.getElementById('background_image').files[0];
                                        if (isEdit && !file) return true;
                                        if (!isEdit && !file) return {
                                            valid: false,
                                            message: 'Background image is required'
                                        };
                                        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                                        if (!allowed.includes(file.type)) return {
                                            valid: false,
                                            message: 'Only JPG, PNG, WEBP allowed'
                                        };
                                        if (file.size > 5242880) return {valid: false, message: 'Max file size is 5MB'};
                                        return true;
                                    }
                                }
                            }
                        },

                        font_file: {
                            validators: {
                                callback: {
                                    callback: function () {
                                        const file = document.getElementById('font_file').files[0];
                                        if (!file) return true;
                                        const allowed = ['ttf', 'otf'];
                                        const ext = file.name.split('.').pop().toLowerCase();
                                        if (!allowed.includes(ext)) return {
                                            valid: false,
                                            message: 'Only TTF and OTF files are allowed'
                                        };
                                        return true;
                                    }
                                }
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
                if (isAdmin && $('#event_id').length) {
                    $('#event_id').select2();

                    $('#event_id').on('change', function () {
                        validator.revalidateField('event_id');
                    });
                }
                document.getElementById('background_image').addEventListener('change', () => {
                    validator.revalidateField('background_image');
                });

                document.getElementById('font_file').addEventListener('change', () => {
                    validator.revalidateField('font_file');
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

        KTUtil.onDOMContentLoaded(() => KTCertificateEdit.init());
    </script>
@endpush
