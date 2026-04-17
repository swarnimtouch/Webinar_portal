@foreach($active_fields as $field)
    @php
        $field_name   = $field->field_name;
        $label        = $field->label;
        $attr_type    = $field->attr_type ?? 'text';
        $input_value  = $field->input_value;
        $is_required  = $field->is_required ? 'required' : '';

        $input_config = $input_value ? json_decode($input_value, true) : null;

        $fieldMapping = [
            'mobile_number'             => 'mobile',
            'alternative_mobile_number' => 'alternative_mobile',
        ];
        $dbField_name = $fieldMapping[$field_name] ?? $field_name;
        $value        = old($dbField_name, isset($user) ? ($user->$dbField_name ?? '') : '');
    @endphp

    <div class="row mb-6">

        <label class="col-lg-4 col-form-label {{ $is_required }} fw-bold fs-6">
            {{ $label }}
            @if($field_name === 'password' && isset($user))
                <span class="text-muted fs-7 fw-normal ms-1">(Leave blank to keep current)</span>
            @endif
        </label>

        <div class="col-lg-8">

            @if($attr_type === 'text')

                @if($field_name === 'email')
                    <input type="email"
                           name="email"
                           value="{{ $value }}"
                           class="form-control form-control-lg form-control-solid"
                           placeholder="Enter email address"/>

                @elseif(in_array($field_name, ['mobile_number', 'alternative_mobile_number']))
                    <input type="tel"
                           name="{{ $dbField_name }}"
                           value="{{ $value }}"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           class="form-control form-control-lg form-control-solid mobile-number-input"
                           placeholder="Enter mobile number"/>

                @elseif($field_name === 'avatar')
                    @php
                        $avatar_url = (isset($user) && $user->avatar)
                            ? Storage::url($user->avatar)
                            : asset('assets/media/avatars/blank.png');
                    @endphp
                    <div class="image-input image-input-outline"
                         data-kt-image-input="true"
                         style="background-image: url('{{ asset('assets/media/avatars/blank.png') }}')">

                        <div class="image-input-wrapper w-125px h-125px"
                             id="avatarPreview"
                             style="background-image: url('{{ $avatar_url }}')">
                        </div>

                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                               data-kt-image-input-action="change"
                               data-bs-toggle="tooltip"
                               title="Change avatar">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="avatar" id="avatar" accept="image/*"/>
                            <input type="hidden" name="avatar_remove"/>
                        </label>

                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                              data-kt-image-input-action="cancel"
                              data-bs-toggle="tooltip"
                              title="Cancel">
                            <i class="bi bi-x fs-2"></i>
                        </span>

                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                              data-kt-image-input-action="remove"
                              data-bs-toggle="tooltip"
                              title="Remove">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>
                    <div class="form-text">Allowed file types: jpg, jpeg, png, gif. Max size: 5MB</div>

                @else
                    <input type="text"
                           name="{{ $dbField_name }}"
                           value="{{ $value }}"
                           class="form-control form-control-lg form-control-solid"
                           placeholder="Enter {{ strtolower($label) }}"/>
                @endif

            @elseif($attr_type === 'textarea')
                <textarea name="{{ $dbField_name }}"
                          class="form-control form-control-lg form-control-solid"
                          rows="3"
                          placeholder="Enter {{ strtolower($label) }}">{{ $value }}</textarea>

            @elseif($attr_type === 'dropdown' || $attr_type === 'select')

                @php
                    $source    = $input_config['source']     ?? $field_name . 's';
                    $dependsOn = $input_config['depends_on'] ?? null;
                @endphp
                <select name="{{ $field_name }}"
                        id="{{ $field_name }}"
                        class="form-select form-select-lg form-select-solid"
                        data-source="{{ $source }}"
                        data-old-value="{{ $value }}"
                        @if($dependsOn) data-depends-on="{{ $dependsOn }}" @endif>
                    <option value="">Select {{ $label }}</option>
                </select>

            @elseif($attr_type === 'password')
                <input type="password"
                       name="password"
                       class="form-control form-control-lg form-control-solid"
                       placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Enter password' }}"
                       autocomplete="new-password"/>

            @elseif($attr_type === 'date')
                <input type="date"
                       name="{{ $dbField_name }}"
                       value="{{ $value }}"
                       class="form-control form-control-lg form-control-solid"/>

            @elseif($attr_type === 'file')
                <input type="file"
                       name="{{ $dbField_name }}"
                       class="form-control form-control-lg form-control-solid"/>
                @if(isset($user) && $user->$dbField_name)
                    <div class="form-text">
                        Current file:
                        <a href="{{ Storage::url($user->$dbField_name) }}" target="_blank">View</a>
                    </div>
                @endif

            @elseif($attr_type === 'checkbox')
                @php
                    $options       = $input_config['options'] ?? [];
                    $checkedValues = is_array($value) ? $value : array_filter(explode(',', $value));
                @endphp
                @forelse($options as $option)
                    @php
                        $opt_val   = is_array($option) ? ($option['value'] ?? $option) : $option;
                        $opt_label = is_array($option) ? ($option['label'] ?? $option) : $option;
                    @endphp
                    <div class="form-check form-check-custom form-check-solid mb-2">
                        <input class="form-check-input"
                               type="checkbox"
                               name="{{ $dbField_name }}[]"
                               value="{{ $opt_val }}"
                               id="{{ $dbField_name }}_{{ $loop->index }}"
                            {{ in_array($opt_val, $checkedValues) ? 'checked' : '' }}/>
                        <label class="form-check-label" for="{{ $dbField_name }}_{{ $loop->index }}">
                            {{ $opt_label }}
                        </label>
                    </div>
                @empty
                    <span class="text-muted">No options available</span>
                @endforelse

            @elseif($attr_type === 'radio')
                @php
                    $options = $input_config['options'] ?? [];
                @endphp
                @forelse($options as $option)
                    @php
                        $opt_val   = is_array($option) ? ($option['value'] ?? $option) : $option;
                        $opt_label = is_array($option) ? ($option['label'] ?? $option) : $option;
                    @endphp
                    <div class="form-check form-check-custom form-check-solid mb-2">
                        <input class="form-check-input"
                               type="radio"
                               name="{{ $dbField_name }}"
                               value="{{ $opt_val }}"
                               id="{{ $dbField_name }}_{{ $loop->index }}"
                            {{ $value == $opt_val ? 'checked' : '' }}/>
                        <label class="form-check-label" for="{{ $dbField_name }}_{{ $loop->index }}">
                            {{ $opt_label }}
                        </label>
                    </div>
                @empty
                    <span class="text-muted">No options available</span>
                @endforelse
            @else
                <input type="text"
                       name="{{ $dbField_name }}"
                       value="{{ $value }}"
                       class="form-control form-control-lg form-control-solid"
                       placeholder="Enter {{ strtolower($label) }}"/>
            @endif

        </div>
    </div>

@endforeach
@php
    $field_names = $active_fields->pluck('field_name')->toArray();
@endphp

@if(!in_array('country', $field_names) && (in_array('state', $field_names) || in_array('city', $field_names)))
    <input type="hidden" name="country" id="country_hidden" value="India">
@endif

@if(!in_array('state', $field_names) && in_array('city', $field_names))
    <input type="hidden" name="state" id="state_hidden" value="Gujarat">
@endif
