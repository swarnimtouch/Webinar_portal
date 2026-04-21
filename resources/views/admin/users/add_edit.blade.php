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
                        <div class="card-title fs-3 fw-bolder">{{ $title }}</div>
                    </div>

                    <div class="card mb-5 mb-xl-10">
                        <div id="kt_user_wrapper" class="collapse show">
                            <form method="POST"
                                  action="{{ route('admin.user.save', isset($user) ? $user->id : '') }}"
                                  id="kt_user_form"
                                  enctype="multipart/form-data">
                                @csrf
                                @if(isset($user))
                                    @method('PUT')
                                @endif

                                <input type="hidden" name="avatar_removed" id="avatar_removed" value="0">
                                <input type="hidden" name="has_existing_avatar" id="has_existing_avatar"
                                       value="{{ isset($user) && $user->avatar ? '1' : '0' }}">

                                <div class="card-body border-top p-9">
                                    @if(auth()->user()->type === 'admin')
                                        <div class="row mb-6">
                                            <label class="col-lg-4 col-form-label required fw-bold fs-6">Event</label>
                                            <div class="col-lg-8">
                                                <select name="event_id" id="event_id"
                                                        class="form-select form-select-solid form-select-lg"
                                                        data-control="select2"
                                                        data-placeholder="Select an Event"
                                                        data-hide-search="true">
                                                    <option value="" disabled selected>Select Event</option>
                                                    @foreach($events as $event)
                                                        <option value="{{ $event->id }}"
                                                            {{ old('event_id', $user->event_id ?? '') == $event->id ? 'selected' : '' }}>
                                                            {{ $event->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <input type="hidden" name="event_id" value="{{ auth()->user()->event_id }}">
                                    @endif

                                    <div id="dynamic_fields_wrapper">
                                        @include('admin.users._dynamic_fields', [
                                            'active_fields' => $active_fields,
                                            'user'          => $user ?? null
                                        ])
                                    </div>
                                </div>

                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <a href="{{ route('admin.user.index') }}"
                                       class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="kt_user_submit">
                                        <span class="indicator-label">Save</span>
                                        <span class="indicator-progress">
                                            Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        "use strict";

        KTUtil.onDOMContentLoaded(function () {

            const form = document.querySelector('#kt_user_form');
            const submit_btn = document.querySelector('#kt_user_submit');
            const avatar_removed_input = document.querySelector('#avatar_removed');
            const EVENT_FIELDS_URL = "{{ url('admin/get-event-fields') }}";
            const CURRENT_EVENT_ID = "{{ isset($user) ? $user->event_id : '' }}";

            let validator = null;

            function reload_dependent($parent) {
                const parentName = $parent.attr('id');
                const parentVal = $parent.find(':selected').val();
                const eventId = $('#event_id').val() || CURRENT_EVENT_ID;

                $('[data-depends-on="' + parentName + '"]').each(function () {
                    const $child = $(this);
                    const fieldName = $child.attr('name');
                    const placeholder = $child.find('option:first').text();
                    $child.html(`<option value="">${placeholder}</option>`);

                    if (!eventId) return;

                    $.get(EVENT_FIELDS_URL + '/' + eventId, {
                        parent_field: parentName,
                        parent_value: parentVal
                    }, function (html) {
                        const $newSelect = $(html).find('select[name="' + fieldName + '"]');
                        if ($newSelect.length) $child.replaceWith($newSelect);
                    });
                });
            }

            $(document).on('change', 'select[data-source]', function () {
                reload_dependent($(this));
            });

            function build_validation() {
                const fields = {};

                $('#dynamic_fields_wrapper [name]').each(function () {
                    const el = this;
                    const name = el.name.replace(/\[\]$/, '');
                    const type = el.type || el.tagName.toLowerCase();
                    const $row = $(el).closest('.row');
                    const label = $row.find('label').first().text().trim().replace(/\(.*?\)/g, '').trim();
                    const required = $row.find('label').first().hasClass('required');

                    if (['hidden', 'submit', 'button', 'file'].includes(type)) return;

                    if (name === 'password') {
                        @if(!isset($user))
                            fields['password'] = {validators: {notEmpty: {message: 'Password is required'}}};
                        @endif
                            return;
                    }

                    if (name === 'email') {
                        const v = {emailAddress: {message: 'Enter a valid email address'}};
                        if (required) v.notEmpty = {message: 'Email is required'};
                        fields['email'] = {validators: v};
                        return;
                    }

                    if (type === 'tel' || $(el).hasClass('mobile-number-input')) {
                        const v = {
                            digits: {message: 'Only numbers are allowed'},
                            stringLength: {min: 10, max: 10, message: 'Mobile number must be 10 digits'}
                        };
                        if (required) v.notEmpty = {message: (label || 'This field') + ' is required'};
                        fields[name] = {validators: v};
                        return;
                    }

                    if (required && !fields[name]) {
                        fields[name] = {validators: {notEmpty: {message: (label || 'This field') + ' is required'}}};
                    }
                });

                if (validator) {
                    try {
                        validator.destroy();
                    } catch (e) {
                    }
                    validator = null;
                }
                if (form.fv) {
                    try {
                        form.fv.destroy();
                    } catch (e) {
                    }
                    delete form.fv;
                }

                setTimeout(function () {
                    validator = FormValidation.formValidation(form, {
                        fields: fields,
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({rowSelector: '.row'})
                        }
                    });
                }, 100);
            }

            $('#event_id').on('change', function () {
                const eventId = $(this).val();

                if (validator) {
                    try {
                        validator.destroy();
                    } catch (e) {
                    }
                    validator = null;
                }
                if (form.fv) {
                    try {
                        form.fv.destroy();
                    } catch (e) {
                    }
                    delete form.fv;
                }

                if (!eventId) {
                    $('#dynamic_fields_wrapper').html('');
                    return;
                }

                $('#dynamic_fields_wrapper').html(`
            <div class="text-center py-5">
                <span class="spinner-border spinner-border-sm me-2"></span> Loading fields...
            </div>
        `);

                $.get(EVENT_FIELDS_URL + '/' + eventId, function (html) {
                    $('#dynamic_fields_wrapper').html(html);
                    build_validation();
                });
            });

            if (avatar_removed_input) {
                $(document).on('click', '[data-kt-image-input-action="remove"]', function () {
                    avatar_removed_input.value = '1';
                });
                $(document).on('click', '[data-kt-image-input-action="cancel"]', function () {
                    avatar_removed_input.value = '0';
                });
                $(document).on('change', '#avatar', function () {
                    if (this.files && this.files.length > 0) avatar_removed_input.value = '0';
                });
            }

            submit_btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (!validator) {
                    form.submit();
                    return;
                }
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submit_btn.setAttribute('data-kt-indicator', 'on');
                        submit_btn.disabled = true;
                        form.submit();
                    }
                });
            });

            if ($('#dynamic_fields_wrapper [name]').length) {
                build_validation();
            }

        });
    </script>
@endpush
