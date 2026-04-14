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
                                    @foreach($activeFields as $field)
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
                                                    <select name="country"
                                                            class="form-select form-select-lg form-select-solid">
                                                        <option value="">Select Country</option>
                                                        @foreach(['India', 'USA', 'UK', 'Canada', 'Australia'] as $country)
                                                            <option
                                                                value="{{ $country }}" {{ $value == $country ? 'selected' : '' }}>
                                                                {{ $country }}
                                                            </option>
                                                        @endforeach
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
                                        <span class="indicator-label">{{ isset($user) ? 'Update' : 'Save' }}</span>
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
            const submitBtn = document.querySelector('#kt_user_submit');
            const avatarInput = document.querySelector('#avatar');
            const avatarRemovedInput = document.querySelector('#avatar_removed');
            const hasExistingAvatar = document.querySelector('#has_existing_avatar').value === '1';
            const isEditMode = {{ isset($user) ? 'true' : 'false' }};

            let avatarWasRemoved = false;
            let newAvatarSelected = false;

            if (avatarRemovedInput) {
                // Remove button
                document.querySelectorAll('[data-kt-image-input-action="remove"]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        avatarWasRemoved = true;
                        newAvatarSelected = false;
                        avatarRemovedInput.value = '1';
                        setTimeout(() => {
                            if (validator) validator.revalidateField('avatar');
                        }, 100);
                    });
                });

                // Cancel button
                document.querySelectorAll('[data-kt-image-input-action="cancel"]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        avatarWasRemoved = false;
                        newAvatarSelected = false;
                        avatarRemovedInput.value = '0';
                        setTimeout(() => {
                            if (validator) validator.revalidateField('avatar');
                        }, 100);
                    });
                });

                // New file selected
                if (avatarInput) {
                    avatarInput.addEventListener('change', function () {
                        if (this.files && this.files.length > 0) {
                            newAvatarSelected = true;
                            avatarWasRemoved = false;
                            avatarRemovedInput.value = '0';
                        }
                    });
                }
            }

            const validationFields = {};

            @foreach($activeFields as $field)
                @php
                    $fieldName    = $field->field_name;
                    $fieldMapping = [
                        'mobile_number'             => 'mobile',
                        'alternative_mobile_number' => 'alternative_mobile',
                    ];
                    $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;
                @endphp

                @if($fieldName == 'avatar')
                validationFields['avatar'] = {
                validators: {
                    @if($field->is_required)
                    callback: {
                        message: 'Avatar is required',
                        callback: function () {
                            if (isEditMode && hasExistingAvatar) {
                                if (avatarWasRemoved && !newAvatarSelected) return false;
                                if (!avatarWasRemoved) return true;
                            }
                            return !!(avatarInput && avatarInput.files && avatarInput.files.length > 0);
                        }
                    },
                    @endif
                    file: {
                        extension: 'jpg,jpeg,png,gif',
                        type: 'image/jpeg,image/png,image/gif',
                        maxSize: 5242880,
                        message: 'Only JPG/PNG/GIF up to 5MB allowed'
                    }
                }
            };

            @elseif($fieldName == 'email')
                validationFields['email'] = {
                validators: {
                    @if($field->is_required)
                    notEmpty: {message: 'Email is required'},
                    @endif
                    emailAddress: {message: 'Please enter a valid email address'}
                }
            };

            @elseif($fieldName == 'password')
                validationFields['password'] = {
                validators: {
                    callback: {
                        callback: function (input) {
                            const value = input.value;

                            // Edit mode mein blank = keep current, koi error nahi
                            if (isEditMode && value === '') return true;

                            @if($field->is_required && !isset($user))
                            if (value === '') return {valid: false, message: 'Password is required'};
                            @endif

                            if (value.length > 0 && value.length < 6) {
                                return {valid: false, message: 'Password must be at least 6 characters'};
                            }

                            return true;
                        }
                    }
                }
            };

            @elseif(in_array($fieldName, ['mobile_number', 'alternative_mobile_number']))
                validationFields['{{ $dbFieldName }}'] = {
                validators: {
                    @if($field->is_required)
                    notEmpty: {message: '{{ $field->label }} is required'},
                    @endif
                    regexp: {
                        regexp: /^[0-9]{10}$/,
                        message: 'Please enter a valid 10-digit mobile number'
                    }
                }
            };

            @elseif($field->is_required)
                validationFields['{{ $dbFieldName }}'] = {
                validators: {
                    notEmpty: {message: '{{ $field->label }} is required'}
                }
            };
            @endif
            @endforeach

            const validator = FormValidation.formValidation(form, {
                fields: validationFields,
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.row'
                    })
                }
            });
            document.querySelectorAll('.mobile-number-input').forEach(function (input) {
                input.addEventListener('keydown', function (e) {
                    const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                        'Home', 'End'];
                    if (allowedKeys.includes(e.key)) return;
                    if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) return;
                    if (!/^[0-9]$/.test(e.key)) e.preventDefault();
                });
                input.addEventListener('paste', function (e) {
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    if (!/^[0-9]+$/.test(pasted)) e.preventDefault();
                });
            });

            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitBtn.setAttribute('data-kt-indicator', 'on');
                        submitBtn.disabled = true;
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
