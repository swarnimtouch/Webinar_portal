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
                                    @foreach($active_fields as $field)
                                        @php
                                            $fieldName   = $field->field_name;
                                            $label       = $field->label;
                                            $isRequired  = $field->is_required ? 'required' : '';

                                            $fieldMapping = [
                                                'mobile_number'             => 'mobile',
                                                'alternative_mobile_number' => 'alternative_mobile',
                                            ];

                                            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;
                                            $value       = old($dbFieldName, $user->$dbFieldName ?? '');
                                        @endphp

                                        @if($fieldName == 'avatar')
                                            @php
                                                $avatarUrl = (isset($user) && $user->avatar)
                                                    ? Storage::url($user->avatar)
                                                    : asset('assets/media/avatars/blank.png');
                                            @endphp
                                            <div class="row mb-6" id="avatarUploadSection">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <div class="image-input image-input-outline"
                                                         data-kt-image-input="true"
                                                         style="background-image: url('{{ asset('assets/media/avatars/blank.png') }}')">

                                                        <div class="image-input-wrapper w-125px h-125px"
                                                             id="avatarPreview"
                                                             style="background-image: url('{{ $avatarUrl }}')">
                                                        </div>

                                                        <label
                                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                            data-kt-image-input-action="change"
                                                            data-bs-toggle="tooltip"
                                                            title="Change avatar">
                                                            <i class="bi bi-pencil-fill fs-7"></i>
                                                            <input type="file" name="avatar" id="avatar"
                                                                   accept="image/*"/>
                                                            <input type="hidden" name="avatar_remove"/>
                                                        </label>

                                                        <span
                                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                            data-kt-image-input-action="cancel"
                                                            data-bs-toggle="tooltip"
                                                            title="Cancel">
                                                            <i class="bi bi-x fs-2"></i>
                                                        </span>

                                                        <span
                                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                            data-kt-image-input-action="remove"
                                                            data-bs-toggle="tooltip"
                                                            title="Remove">
                                                            <i class="bi bi-x fs-2"></i>
                                                        </span>
                                                    </div>
                                                    <div class="form-text">Allowed file types: jpg, jpeg, png, gif. Max
                                                        size: 5MB
                                                    </div>
                                                </div>
                                            </div>

                                        @elseif($fieldName == 'email')
                                            {{-- Email Field --}}
                                            <div class="row mb-6">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <input type="email"
                                                           name="email"
                                                           value="{{ $value }}"
                                                           class="form-control form-control-lg form-control-solid"
                                                           placeholder="Enter email address"/>
                                                </div>
                                            </div>

                                        @elseif(in_array($fieldName, ['mobile_number', 'alternative_mobile_number']))
                                            {{-- Mobile Number Fields --}}
                                            <div class="row mb-6">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <input type="tel"
                                                           name="{{ $dbFieldName }}"
                                                           value="{{ $value }}"
                                                           inputmode="numeric"
                                                           pattern="[0-9]*"
                                                           class="form-control form-control-lg form-control-solid mobile-number-input"
                                                           placeholder="Enter mobile number"/>
                                                </div>
                                            </div>

                                        @elseif($fieldName == 'password')
                                            {{-- Password Field --}}
                                            <div class="row mb-6">
                                                <label
                                                    class="col-lg-4 col-form-label {{ isset($user) ? '' : $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                    @if(isset($user))
                                                        <span class="text-muted fs-7 fw-normal ms-1">(Leave blank to keep current)</span>
                                                    @endif
                                                </label>
                                                <div class="col-lg-8">
                                                    <input type="password"
                                                           name="password"
                                                           class="form-control form-control-lg form-control-solid"
                                                           placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Enter password' }}"
                                                           autocomplete="new-password"/>
                                                </div>
                                            </div>

                                        @elseif($fieldName == 'address')
                                            {{-- Address Textarea --}}
                                            <div class="row mb-6">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <textarea name="address"
                                                              class="form-control form-control-lg form-control-solid"
                                                              rows="3"
                                                              placeholder="Enter address">{{ $value }}</textarea>
                                                </div>
                                            </div>

                                        @elseif($fieldName == 'country')
                                            {{-- Country Dropdown --}}
                                            <div class="row mb-6">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <select name="country" id="country"
                                                            class="form-select form-select-lg form-select-solid">
                                                        <option value="">Select Country</option>
                                                    </select>
                                                </div>
                                            </div>

                                        @elseif($fieldName == 'state')
                                            {{-- State Dropdown --}}
                                            <div class="row mb-6">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <select name="state" id="state"
                                                            class="form-select form-select-lg form-select-solid">
                                                        <option value="">Select State</option>
                                                    </select>
                                                </div>
                                            </div>

                                        @elseif($fieldName == 'city')
                                            {{-- City Dropdown --}}
                                            <div class="row mb-6">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <select name="city" id="city"
                                                            class="form-select form-select-lg form-select-solid">
                                                        <option value="">Select City</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Default Text Input --}}
                                            <div class="row mb-6">
                                                <label class="col-lg-4 col-form-label {{ $isRequired }} fw-bold fs-6">
                                                    {{ $label }}
                                                </label>
                                                <div class="col-lg-8">
                                                    <input type="text"
                                                           name="{{ $dbFieldName }}"
                                                           value="{{ $value }}"
                                                           class="form-control form-control-lg form-control-solid"
                                                           placeholder="Enter {{ strtolower($label) }}"/>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
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
            const avatar_input = document.querySelector('#avatar');
            const avatar_removed_input = document.querySelector('#avatar_removed');
            const has_existing_avatar = document.querySelector('#has_existing_avatar').value === '1';
            const is_edit_mode = {{ isset($user) ? 'true' : 'false' }};

            let avatar_was_removed = false;
            let new_avatar_selected = false;

            if (avatar_removed_input) {

                document.querySelectorAll('[data-kt-image-input-action="remove"]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        avatar_was_removed = true;
                        new_avatar_selected = false;
                        avatar_removed_input.value = '1';

                        setTimeout(() => {
                            if (validator) validator.revalidateField('avatar');
                        }, 100);
                    });
                });

                document.querySelectorAll('[data-kt-image-input-action="cancel"]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        avatar_was_removed = false;
                        new_avatar_selected = false;
                        avatar_removed_input.value = '0';
                    });
                });

                if (avatar_input) {
                    avatar_input.addEventListener('change', function () {
                        if (this.files && this.files.length > 0) {
                            new_avatar_selected = true;
                            avatar_was_removed = false;
                            avatar_removed_input.value = '0';
                        }
                    });
                }
            }

            const validation_fields = {};

            @foreach($active_fields as $field)
                @php
                    $field_name = $field->field_name;
                    $field_mapping = [
                        'mobile_number' => 'mobile',
                        'alternative_mobile_number' => 'alternative_mobile',
                    ];
                    $db_field_name = $field_mapping[$field_name] ?? $field_name;
                @endphp

                @if($field_name == 'email')
                validation_fields['email'] = {
                validators: {
                    @if($field->is_required)
                    notEmpty: {message: 'Email is required'},
                    @endif
                    emailAddress: {message: 'Enter valid email'}
                }
            };
            @elseif($field->is_required)
                validation_fields['{{ $db_field_name }}'] = {
                validators: {
                    notEmpty: {message: '{{ $field->label }} is required'}
                }
            };
            @endif
            @endforeach

            const validator = FormValidation.formValidation(form, {
                fields: validation_fields,
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.row'
                    })
                }
            });

            submit_btn.addEventListener('click', function (e) {
                e.preventDefault();

                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submit_btn.setAttribute('data-kt-indicator', 'on');
                        submit_btn.disabled = true;
                        form.submit();
                    }
                });
            });


            window.old_country = "{{ old('country', $user->country ?? '') }}";
            window.old_state = "{{ old('state', $user->state ?? '') }}";
            window.old_city = "{{ old('city', $user->city ?? '') }}";

            $.get("{{ route('admin.users.countries') }}", function (countries) {

                $('#country').append(
                    countries.map(c => `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`)
                );

                if (window.old_country) {
                    $('#country').val(window.old_country);
                }

                const selected_country = countries.find(c => c.name === window.old_country);

                if (selected_country) {
                    load_states(selected_country.id);
                }
            });

            function load_states(country_id) {

                $.get(`/get-states/${country_id}`, function (states) {

                    $('#state').html('<option value="">Select State</option>').append(
                        states.map(s => `<option value="${s.name}" data-id="${s.id}">${s.name}</option>`)
                    );

                    if (window.old_state) {
                        $('#state').val(window.old_state);
                    }

                    const selected_state = states.find(s => s.name === window.old_state);

                    if (selected_state) {
                        load_cities(selected_state.id);
                    }
                });
            }

            function load_cities(state_id) {

                $.get(`/get-cities/${state_id}`, function (cities) {

                    $('#city').html('<option value="">Select City</option>').append(
                        cities.map(c => `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`)
                    );

                    if (window.old_city) {
                        $('#city').val(window.old_city);
                    }
                });
            }

            $('#country').on('change', function () {
                const country_id = $(this).find(':selected').data('id');

                $('#state').html('<option value="">Select State</option>');
                $('#city').html('<option value="">Select City</option>');

                if (country_id) load_states(country_id);
            });

            $('#state').on('change', function () {
                const state_id = $(this).find(':selected').data('id');

                $('#city').html('<option value="">Select City</option>');

                if (state_id) load_cities(state_id);
            });

        });
    </script>
@endpush
