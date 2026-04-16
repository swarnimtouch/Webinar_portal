@extends('layouts.admin')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">


                <div class="card mb-5 mb-xl-10">


                    <div id="kt_account_profile_details" class="collapse show">
                        <form method="POST" action="{{ route('admin.settings.save') }}"
                              id="kt_settings_form" enctype="multipart/form-data">
                            @csrf

                            <div class="card-body border-top p-9">
                                @foreach($fields as $field)
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-bold fs-6
                                        {{ !empty($field['extra']) && strpos($field['extra'], '"required":"required"') !== false ? 'required' : '' }}">
                                            {{ $field['label'] }}
                                        </label>

                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row">
                                                    @php
                                                        $type = $field['type'];
                                                        $extra_array = !empty($field['extra']) ? json_decode($field['extra'], true) : [];
                                                        $is_required = isset($extra_array['required']) && $extra_array['required'] === 'required';

                                                        $options_array = [];
                                                        if (!empty($field['options'])) {
                                                            if (is_string($field['options'])) {
                                                                $decoded = json_decode($field['options'], true);
                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                    $options_array = $decoded;
                                                                } else {
                                                                    $items = explode(',', $field['options']);
                                                                    foreach ($items as $item) {
                                                                        $item = trim($item);
                                                                        $options_array[$item] = ucfirst($item);
                                                                    }
                                                                }
                                                            } else {
                                                                $options_array = $field['options'];
                                                            }
                                                        }
                                                    @endphp

                                                    @if(in_array($type, ['text','number','email','url']))
                                                        <input type="{{ $type }}"
                                                               name="{{ $field['unique_name'] }}"
                                                               id="{{ $field['unique_name'] }}"
                                                               class="form-control form-control-lg form-control-solid"
                                                               placeholder="{{ $field['label'] }}"
                                                               value="{{ old($field['unique_name'], $field['value']) }}"
                                                        {{ $is_required ? 'required' : '' }}
                                                        @if(!empty($extra_array))
                                                            @foreach($extra_array as $k => $v)
                                                                @if($k !== 'required')
                                                                    {{ $k }}="{{ $v }}"
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        >

                                                    @elseif($type === 'textarea')
                                                        <textarea name="{{ $field['unique_name'] }}"
                                                                  id="{{ $field['unique_name'] }}"
                                                                  class="form-control form-control-lg form-control-solid"
                                                                  placeholder="{{ $field['label'] }}"
                                                                  rows="4"
                                                        {{ $is_required ? 'required' : '' }}
                                                        @if(!empty($extra_array))
                                                            @foreach($extra_array as $k => $v)
                                                                @if($k !== 'required')
                                                                    {{ $k }}="{{ $v }}"
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        >{{ old($field['unique_name'], $field['value']) }}</textarea>

                                                    @elseif($type === 'file')
                                                        <input type="file"
                                                               name="{{ $field['unique_name'] }}"
                                                               id="{{ $field['unique_name'] }}"
                                                               class="form-control form-control-lg form-control-solid"
                                                        {{ $is_required && empty($field['value']) ? 'required' : '' }}
                                                        @if(!empty($extra_array))
                                                            @foreach($extra_array as $k => $v)
                                                                @if($k !== 'required')
                                                                    {{ $k }}="{{ $v }}"
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        >
                                                        @if(!empty($field['value']))
                                                            @php
                                                                $file = $field['value'];
                                                                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                                $fileUrl = asset('storage/site_settings/'.$file);
                                                            @endphp

                                                            <div class="mt-3">
                                                                @if(in_array($extension, ['jpg','jpeg','png','gif','webp']))
                                                                    <img
                                                                        src="{{ $fileUrl }}"
                                                                        class="img-fluid"
                                                                        style="max-height:60px">

                                                                @elseif($extension == 'pdf')
                                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                                       class="btn btn-sm btn-primary">
                                                                        View PDF
                                                                    </a>
                                                                @else
                                                                    <a href="{{ $fileUrl }}" target="_blank">
                                                                        Download File
                                                                    </a>
                                                                @endif

                                                            </div>
                                                        @endif

                                                    @elseif($type === 'radio')
                                                        <div class="radio-group">
                                                            @if(!empty($options_array))
                                                                @foreach($options_array as $option_value => $option_label)
                                                                    <div
                                                                        class="form-check form-check-custom form-check-solid mb-3">
                                                                        <input class="form-check-input"
                                                                               type="radio"
                                                                               name="{{ $field['unique_name'] }}"
                                                                               id="{{ $field['unique_name'] }}_{{ $option_value }}"
                                                                               value="{{ $option_value }}"
                                                                            {{ old($field['unique_name'], $field['value']) == $option_value ? 'checked' : '' }}
                                                                            {{ $is_required ? 'required' : '' }}
                                                                        >
                                                                        <label class="form-check-label"
                                                                               for="{{ $field['unique_name'] }}_{{ $option_value }}">
                                                                            {{ $option_label }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <p class="text-muted">No options available</p>
                                                            @endif
                                                        </div>

                                                    @elseif($type === 'checkbox')
                                                        <div class="checkbox-group">
                                                            @if(!empty($options_array))
                                                                @php
                                                                    $selected_values = !empty($field['value']) ? json_decode($field['value'], true) : [];
                                                                    if (!is_array($selected_values)) {
                                                                        $selected_values = [$selected_values];
                                                                    }
                                                                @endphp
                                                                @foreach($options_array as $option_value => $option_label)
                                                                    <div
                                                                        class="form-check form-check-custom form-check-solid mb-3">
                                                                        <input class="form-check-input checkbox-item"
                                                                               type="checkbox"
                                                                               name="{{ $field['unique_name'] }}[]"
                                                                               id="{{ $field['unique_name'] }}_{{ $option_value }}"
                                                                               value="{{ $option_value }}"
                                                                               {{ in_array($option_value, $option_values) ? 'checked' : '' }}
                                                                               data-group="{{ $field['unique_name'] }}"
                                                                            {{ $is_required ? 'data-required=true' : '' }}
                                                                        >
                                                                        <label class="form-check-label"
                                                                               for="{{ $field['unique_name'] }}_{{ $option_value }}">
                                                                            {{ $option_label }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <p class="text-muted">No options available</p>
                                                            @endif
                                                        </div>
                                                    @elseif($type === 'select')
                                                        <select name="{{ $field['unique_name'] }}"
                                                                id="{{ $field['unique_name'] }}"
                                                                class="form-select form-select-lg form-select-solid"
                                                        {{ $is_required ? 'required' : '' }}
                                                        @if(!empty($extra_array))
                                                            @foreach($extra_array as $k => $v)
                                                                @if($k !== 'required')
                                                                    {{ $k }}="{{ $v }}"
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        >
                                                        <option value="">Select {{ $field['label'] }}</option>
                                                        @if(!empty($options_array))
                                                            @foreach($options_array as $option_value => $option_label)
                                                                <option value="{{ $option_value }}"
                                                                    {{ old($field['unique_name'], $field['value']) == $option_value ? 'selected' : '' }}
                                                                >
                                                                    {{ $option_label }}
                                                                </option>
                                                                @endforeach
                                                                @endif
                                                                </select>

                                                                @endif

                                                                @if(!empty($field['hint']))
                                                                    <div class="form-text">{{ $field['hint'] }}</div>
                                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <button type="submit" class="btn btn-primary" id="kt_settings_submit">Save</button>
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

                var KTSettingsUpdate = function () {
                    var form;
                    var submitButton;
                    var validator;

                    return {
                        init: function () {
                            form = document.querySelector("#kt_settings_form");
                            submitButton = document.querySelector("#kt_settings_submit");

                            if (!form) return;

                            var validationFields = {};

                            var inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
                            inputs.forEach(function (input) {
                                var fieldName = input.getAttribute('name');
                                var fieldType = input.getAttribute('type');

                                if (!fieldName) return;

                                var validationName = fieldName.replace('[]', '');

                                if (fieldType === 'email') {
                                    validationFields[validationName] = {
                                        validators: {
                                            notEmpty: {
                                                message: input.placeholder + ' is required'
                                            },
                                            emailAddress: {
                                                message: 'Please enter a valid email address'
                                            }
                                        }
                                    };
                                } else if (fieldType === 'number') {
                                    validationFields[validationName] = {
                                        validators: {
                                            notEmpty: {
                                                message: input.placeholder + ' is required'
                                            },
                                            numeric: {
                                                message: 'Please enter a valid number'
                                            }
                                        }
                                    };
                                } else if (fieldType === 'url') {
                                    validationFields[validationName] = {
                                        validators: {
                                            notEmpty: {
                                                message: input.placeholder + ' is required'
                                            },
                                            uri: {
                                                message: 'Please enter a valid URL'
                                            }
                                        }
                                    };
                                } else if (fieldType === 'file') {
                                    var acceptAttr = input.getAttribute('accept');
                                    validationFields[validationName] = {
                                        validators: {
                                            notEmpty: {
                                                message: 'Please select a file'
                                            },
                                            file: {
                                                extension: acceptAttr ? acceptAttr.replace(/image\//g, '').replace(/\*/g, 'jpg,jpeg,png,gif') : 'jpg,jpeg,png,gif,pdf,doc,docx',
                                                type: acceptAttr || 'image/jpeg,image/png,image/gif',
                                                maxSize: 5242880, // 5MB
                                                message: 'Please select a valid file (max 5MB)'
                                            }
                                        }
                                    };
                                } else if (input.tagName === 'SELECT') {
                                    validationFields[validationName] = {
                                        validators: {
                                            notEmpty: {
                                                message: 'Please select ' + input.closest('.row').querySelector('label').textContent.trim()
                                            }
                                        }
                                    };
                                } else {
                                    validationFields[validationName] = {
                                        validators: {
                                            notEmpty: {
                                                message: (input.placeholder || 'This field') + ' is required'
                                            }
                                        }
                                    };
                                }
                            });

                            if (Object.keys(validationFields).length > 0) {
                                validator = FormValidation.formValidation(form, {
                                    fields: validationFields,
                                    plugins: {
                                        trigger: new FormValidation.plugins.Trigger(),
                                        bootstrap: new FormValidation.plugins.Bootstrap5({
                                            rowSelector: '.fv-row',
                                            eleInvalidClass: '',
                                            eleValidClass: ''
                                        })
                                    }
                                });
                            }

                            var checkboxGroups = {};
                            document.querySelectorAll('.checkbox-item[data-required="true"]').forEach(function (checkbox) {
                                var group = checkbox.getAttribute('data-group');
                                if (!checkboxGroups[group]) {
                                    checkboxGroups[group] = [];
                                }
                                checkboxGroups[group].push(checkbox);
                            });

                            submitButton.addEventListener("click", function (e) {
                                e.preventDefault();

                                var checkboxValid = true;
                                Object.keys(checkboxGroups).forEach(function (group) {
                                    var checkboxes = checkboxGroups[group];
                                    var checked = checkboxes.filter(function (cb) {
                                        return cb.checked;
                                    });

                                    if (checked.length === 0) {
                                        checkboxValid = false;
                                        var container = checkboxes[0].closest('.checkbox-group');
                                        var errorMsg = container.querySelector('.checkbox-error');
                                        if (!errorMsg) {
                                            errorMsg = document.createElement('div');
                                            errorMsg.className = 'fv-plugins-message-container invalid-feedback checkbox-error';
                                            errorMsg.innerHTML = '<div>Please select at least one option</div>';
                                            container.appendChild(errorMsg);
                                        }
                                        container.classList.add('is-invalid');
                                    } else {
                                        var container = checkboxes[0].closest('.checkbox-group');
                                        var errorMsg = container.querySelector('.checkbox-error');
                                        if (errorMsg) errorMsg.remove();
                                        container.classList.remove('is-invalid');
                                    }
                                });

                                if (!checkboxValid) {
                                    return;
                                }

                                if (validator) {
                                    validator.validate().then(function (status) {
                                        if (status === 'Valid') {
                                            submitButton.setAttribute("data-kt-indicator", "on");
                                            submitButton.disabled = true;
                                            form.submit();
                                        }
                                    });
                                } else {
                                    submitButton.setAttribute("data-kt-indicator", "on");
                                    submitButton.disabled = true;
                                    form.submit();
                                }
                            });
                        }
                    };
                }();

                KTUtil.onDOMContentLoaded(function () {
                    KTSettingsUpdate.init();
                });
            </script>
    @endpush
