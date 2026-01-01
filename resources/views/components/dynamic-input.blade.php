{{--
    Dynamic Input Renderer Component
    Usage: @include('components.dynamic-input', ['field' => $field, 'formType' => 'login'])
--}}

@php
    $inputName = $field->field_name === 'mobile_number' ? 'mobile' : $field->field_name;
    $inputType = $field->input_type ?? 1; // Default to Short Text
    $options = $field->options ? json_decode($field->options, true) : [];
@endphp

<div class="email-input-group">
    <div class="icon-box">
        <i class="{{ $field->icon ?? 'fa-solid fa-user' }}"></i>
    </div>

    @switch($inputType)
        {{-- 1. Short Text (up to 70 characters) --}}
        @case(1)
            <input type="text"
                   name="{{ $inputName }}"
                   placeholder="{{ $field->label }}"
                   maxlength="70"
                   data-field="{{ $field->field_name }}"
                   data-label="{{ $field->label }}"
                   data-is-required="{{ $field->is_required }}"
                   class="@error($inputName) is-invalid @enderror"
                   value="{{ old($inputName) }}" />
            @break

            {{-- 2. Long Text (up to 300 characters) --}}
        @case(2)
            <textarea name="{{ $inputName }}"
                      placeholder="{{ $field->label }}"
                      maxlength="300"
                      rows="4"
                      data-field="{{ $field->field_name }}"
                      data-label="{{ $field->label }}"
                      data-is-required="{{ $field->is_required }}"
                      class="@error($inputName) is-invalid @enderror">{{ old($inputName) }}</textarea>
            @break

            {{-- 3. Single Select Answer --}}
        @case(3)
            <select name="{{ $inputName }}"
                    data-field="{{ $field->field_name }}"
                    data-label="{{ $field->label }}"
                    data-is-required="{{ $field->is_required }}"
                    class="@error($inputName) is-invalid @enderror">
                <option value="">Select {{ $field->label }}</option>
                @foreach($options as $option)
                    <option value="{{ $option }}" {{ old($inputName) == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            @break

            {{-- 4. Multi Select Answer --}}
        @case(4)
            <select name="{{ $inputName }}[]"
                    multiple
                    data-field="{{ $field->field_name }}"
                    data-label="{{ $field->label }}"
                    data-is-required="{{ $field->is_required }}"
                    class="multi-select @error($inputName) is-invalid @enderror">
                @foreach($options as $option)
                    <option value="{{ $option }}"
                        {{ in_array($option, old($inputName, [])) ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            @break

            {{-- 5. Date Field --}}
        @case(5)
            <input type="date"
                   name="{{ $inputName }}"
                   placeholder="{{ $field->label }}"
                   data-field="{{ $field->field_name }}"
                   data-label="{{ $field->label }}"
                   data-is-required="{{ $field->is_required }}"
                   class="@error($inputName) is-invalid @enderror"
                   value="{{ old($inputName) }}" />
            @break

            {{-- 6. File Upload --}}
        @case(6)
            <input type="file"
                   name="{{ $inputName }}"
                   accept="{{ $field->accept ?? '*' }}"
                   data-field="{{ $field->field_name }}"
                   data-label="{{ $field->label }}"
                   data-is-required="{{ $field->is_required }}"
                   class="file-input @error($inputName) is-invalid @enderror" />
            @break

            {{-- 7. Password --}}
        @case(7)
            <input type="password"
                   name="{{ $inputName }}"
                   placeholder="{{ $field->label }}"
                   data-field="{{ $field->field_name }}"
                   data-label="{{ $field->label }}"
                   data-is-required="{{ $field->is_required }}"
                   class="@error($inputName) is-invalid @enderror" />
            @break

            {{-- 8. Login With (Email/Mobile) --}}
        @case(8)
            <input type="text"
                   name="{{ $inputName }}"
                   placeholder="{{ $field->label }}"
                   data-field="{{ $field->field_name }}"
                   data-label="{{ $field->label }}"
                   data-is-required="{{ $field->is_required }}"
                   data-login-type="email_or_mobile"
                   class="@error($inputName) is-invalid @enderror"
                   value="{{ old($inputName) }}" />
            @break

            {{-- 9. Check boxes --}}
        @case(9)
            <div class="checkbox-group">
                @foreach($options as $option)
                    <label class="checkbox-label">
                        <input type="checkbox"
                               name="{{ $inputName }}[]"
                               value="{{ $option }}"
                            {{ in_array($option, old($inputName, [])) ? 'checked' : '' }}>
                        <span>{{ $option }}</span>
                    </label>
                @endforeach
            </div>
            @break

            {{-- 10. Consent --}}
        @case(10)
            <label class="consent-label">
                <input type="checkbox"
                       name="{{ $inputName }}"
                       value="1"
                       data-field="{{ $field->field_name }}"
                       data-label="{{ $field->label }}"
                       data-is-required="{{ $field->is_required }}"
                    {{ old($inputName) ? 'checked' : '' }}>
                <span>{!! $field->label !!}</span>
            </label>
            @break

            {{-- 11. Radio buttons --}}
        @case(11)
            <div class="radio-group">
                @foreach($options as $option)
                    <label class="radio-label">
                        <input type="radio"
                               name="{{ $inputName }}"
                               value="{{ $option }}"
                               data-field="{{ $field->field_name }}"
                               data-label="{{ $field->label }}"
                               data-is-required="{{ $field->is_required }}"
                            {{ old($inputName) == $option ? 'checked' : '' }}>
                        <span>{{ $option }}</span>
                    </label>
                @endforeach
            </div>
            @break

            {{-- 12. Date and Time Field --}}
        @case(12)
            <input type="datetime-local"
                   name="{{ $inputName }}"
                   placeholder="{{ $field->label }}"
                   data-field="{{ $field->field_name }}"
                   data-label="{{ $field->label }}"
                   data-is-required="{{ $field->is_required }}"
                   class="@error($inputName) is-invalid @enderror"
                   value="{{ old($inputName) }}" />
            @break

            {{-- Default: Short Text --}}
        @default
            <input type="text"
                   name="{{ $inputName }}"
                   placeholder="{{ $field->label }}"
                   data-field="{{ $field->field_name }}"
                   data-label="{{ $field->label }}"
                   data-is-required="{{ $field->is_required }}"
                   class="@error($inputName) is-invalid @enderror"
                   value="{{ old($inputName) }}" />
    @endswitch

    {{-- Server-side Error --}}
    @error($inputName)
    <div class="error-text">
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ $message }}
    </div>
    @enderror

    {{-- JS Validation Error --}}
    <div class="error-text js-error"></div>
</div>
