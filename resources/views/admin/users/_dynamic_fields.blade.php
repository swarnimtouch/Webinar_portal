@foreach($active_fields as $field)
    @php
        $field_name   = $field->field_name;
        $label        = $field->label;
        $attr_type    = $field->attribute_data->type ?? 'text';
        $is_required  = $field->is_required ? 'required' : '';

        $input_config = $field->input_value ? json_decode($field->input_value, true) : null;

        $field_mapping = ['mobile_number' => 'mobile'];
        $dbField_name  = $field_mapping[$field_name] ?? $field_name;
        $value         = old($dbField_name, isset($user) ? ($user->$dbField_name ?? '') : '');
    @endphp

    <div class="row mb-6">
        <label class="col-lg-4 col-form-label {{ $is_required }} fw-bold fs-6">
            {{ $label }}
            @if($field_name === 'password' && isset($user))
                <span class="text-muted fs-7 fw-normal ms-1">(Leave blank to keep current)</span>
            @endif
        </label>

        <div class="col-lg-8">
            @switch($attr_type)

                @case('text')
                    @if($field_name === 'email')
                        <input type="email" name="email" value="{{ $value }}"
                               class="form-control form-control-lg form-control-solid"
                               placeholder="Enter email address"/>
                    @elseif(in_array($field_name, ['mobile_number', 'alternative_mobile_number']))
                        <input type="tel" name="{{ $dbField_name }}" value="{{ $value }}"
                               inputmode="numeric" pattern="[0-9]*"
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
                            <div class="image-input-wrapper w-125px h-125px" id="avatarPreview"
                                 style="background-image: url('{{ $avatar_url }}')"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="avatar" id="avatar" accept="image/*"/>
                                <input type="hidden" name="avatar_remove"/>
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                        <div class="form-text">Allowed file types: jpg, jpeg, png, gif. Max size: 5MB</div>
                    @else
                        <input type="text" name="{{ $dbField_name }}" value="{{ $value }}"
                               class="form-control form-control-lg form-control-solid"
                               placeholder="Enter {{ strtolower($label) }}"/>
                    @endif
                    @break

                @case('textarea')
                    <textarea name="{{ $dbField_name }}" rows="3"
                              class="form-control form-control-lg form-control-solid"
                              placeholder="Enter {{ strtolower($label) }}">{{ $value }}</textarea>
                    @break

                @case('password')
                    <input type="password" name="password"
                           class="form-control form-control-lg form-control-solid"
                           placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Enter password' }}"
                           autocomplete="new-password"/>
                    @break

                @case('select')
                @case('dropdown')
                    @php
                        $fkMap = [
                            'countries' => 'country_id',
                            'states'    => 'state_id',
                            'cities'    => 'city_id',
                        ];

                        $source_table   = $input_config['source']     ?? null;
                        $source_val     = $input_config['value']      ?? 'id';
                        $source_lbl     = $input_config['label']      ?? 'name';
                        $dependsOn      = $input_config['depends_on'] ?? null;
                        $source_items   = collect();
                        $parentIsActive = false;
                        $parentValue    = '';

                        if ($source_table) {
                            $query            = \Illuminate\Support\Facades\DB::table($source_table);
                            $field_names_list = $active_fields->pluck('field_name')->toArray();

                            if ($dependsOn) {
                                $parentIsActive = in_array($dependsOn, $field_names_list);

                                if ($parentIsActive) {
                                    if (isset($parent_field) && $parent_field === $dependsOn) {
                                        $parentValue = $parent_value ?? '';
                                    } else {
                                        $parentValue = old($dependsOn, isset($user) ? ($user->$dependsOn ?? '') : '');
                                    }

                                    if ($parentValue) {
                                        $parentFieldConfig = $active_fields->firstWhere('field_name', $dependsOn);
                                        $parentTable       = $parentFieldConfig
                                            ? (json_decode($parentFieldConfig->input_value, true)['source'] ?? null)
                                            : null;
                                        $parentSourceLbl   = $parentFieldConfig
                                            ? (json_decode($parentFieldConfig->input_value, true)['label'] ?? 'name')
                                            : 'name';

                                        if ($parentTable) {
                                            // parentValue is name, find its id first
                                            $parentRow = \Illuminate\Support\Facades\DB::table($parentTable)
                                                ->where($parentSourceLbl, $parentValue)
                                                ->first();
                                            if ($parentRow) {
                                                $fkCol = $fkMap[$parentTable] ?? (rtrim($parentTable, 's') . '_id');
                                                $query->where($fkCol, $parentRow->id);
                                            }
                                        }
                                    }
                                } else {
                                    $defaultParentMap = [
                                        'country' => ['value' => 'India',   'table' => 'countries', 'col' => 'name'],
                                        'state'   => ['value' => 'Gujarat', 'table' => 'states',    'col' => 'name'],
                                    ];

                                    $defaultParent = $defaultParentMap[$dependsOn] ?? null;

                                    if ($defaultParent) {
                                        $parentRow = \Illuminate\Support\Facades\DB::table($defaultParent['table'])
                                            ->where($defaultParent['col'], $defaultParent['value'])
                                            ->first();
                                        if ($parentRow) {
                                            $fkCol = $fkMap[$defaultParent['table']] ?? (rtrim($defaultParent['table'], 's') . '_id');
                                            $query->where($fkCol, $parentRow->id);
                                        }
                                    }
                                }
                            }

                            if (!($dependsOn && $parentIsActive && $parentValue === '')) {
                                $source_items = $query->get();
                            }
                        }
                    @endphp

                    <select name="{{ $field_name }}" id="{{ $field_name }}"
                            class="form-select form-select-lg form-select-solid"
                            data-source="{{ $source_table }}"
                            data-source-val="{{ $source_val }}"
                            data-source-lbl="{{ $source_lbl }}"
                            data-old-value="{{ $value }}"
                            @if($dependsOn && in_array($dependsOn, $active_fields->pluck('field_name')->toArray()))
                                data-depends-on="{{ $dependsOn }}"
                        @endif>
                        <option value="">Select {{ $label }}</option>
                        @foreach($source_items as $item)
                            <option value="{{ $item->{$source_lbl} }}" {{-- name as value --}}
                            data-id="{{ $item->id }}"
                                {{ $value == $item->{$source_lbl} ? 'selected' : '' }}>  {{-- compare name with name --}}
                                {{ $item->{$source_lbl} }}
                            </option>
                        @endforeach
                    </select>
                    @break

                @case('date')
                    <input type="date" name="{{ $dbField_name }}" value="{{ $value }}"
                           class="form-control form-control-lg form-control-solid"/>
                    @break

                @case('file')
                    <input type="file" name="{{ $dbField_name }}"
                           class="form-control form-control-lg form-control-solid"/>
                    @if(isset($user) && $user->$dbField_name)
                        <div class="form-text">
                            Current file: <a href="{{ Storage::url($user->$dbField_name) }}" target="_blank">View</a>
                        </div>
                    @endif
                    @break

                @case('checkbox')
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
                            <input class="form-check-input" type="checkbox"
                                   name="{{ $dbField_name }}[]" value="{{ $opt_val }}"
                                   id="{{ $dbField_name }}_{{ $loop->index }}"
                                {{ in_array($opt_val, $checkedValues) ? 'checked' : '' }}/>
                            <label class="form-check-label" for="{{ $dbField_name }}_{{ $loop->index }}">
                                {{ $opt_label }}
                            </label>
                        </div>
                    @empty
                        <span class="text-muted">No options available</span>
                    @endforelse
                    @break

                @case('radio')
                    @php
                        $options = $input_config['options'] ?? [];
                    @endphp
                    @forelse($options as $option)
                        @php
                            $opt_val   = is_array($option) ? ($option['value'] ?? $option) : $option;
                            $opt_label = is_array($option) ? ($option['label'] ?? $option) : $option;
                        @endphp
                        <div class="form-check form-check-custom form-check-solid mb-2">
                            <input class="form-check-input" type="radio"
                                   name="{{ $dbField_name }}" value="{{ $opt_val }}"
                                   id="{{ $dbField_name }}_{{ $loop->index }}"
                                {{ $value == $opt_val ? 'checked' : '' }}/>
                            <label class="form-check-label" for="{{ $dbField_name }}_{{ $loop->index }}">
                                {{ $opt_label }}
                            </label>
                        </div>
                    @empty
                        <span class="text-muted">No options available</span>
                    @endforelse
                    @break

                @default
                    <input type="text" name="{{ $dbField_name }}" value="{{ $value }}"
                           class="form-control form-control-lg form-control-solid"
                           placeholder="Enter {{ strtolower($label) }}"/>

            @endswitch
        </div>
    </div>
@endforeach

@php
    $field_names_all = $active_fields->pluck('field_name')->toArray();
    $has_country     = in_array('country', $field_names_all);
    $has_state       = in_array('state',   $field_names_all);
    $has_city        = in_array('city',    $field_names_all);
@endphp

@if(!$has_country && ($has_state || $has_city))
    @php $defaultCountry = \Illuminate\Support\Facades\DB::table('countries')->where('name','India')->first(); @endphp
    <input type="hidden" name="country" id="country_hidden"
           value="India" data-id="{{ $defaultCountry->id ?? '' }}">
@endif

@if(!$has_state && $has_city)
    @php $defaultState = \Illuminate\Support\Facades\DB::table('states')->where('name','Gujarat')->first(); @endphp
    <input type="hidden" name="state" id="state_hidden"
           value="Gujarat" data-id="{{ $defaultState->id ?? '' }}">
@endif
