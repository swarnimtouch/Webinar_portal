@extends('layouts.website')
@section('body')
    <main>
        <div class="container">
            <div class="header-section">
                <h1>{{app('event')->name ?? 'Webinar Portal'}}</h1>
                <button class="btn btn-gold open-register-modal {{ app('event') && (app('event')->start_time || app('event')->end_time) ? 'registration-header-button' : '' }}" id="openRegisterModal">Register</button>
            </div>

            <div class="intro-card">
                <div class="hero-banner">

                    <div class="banner-backdrop">
                        <img id="bgImage" class="bg-media" src="" alt="">
                        <video id="bgVideo" class="bg-media" muted loop playsinline></video>
                    </div>

                    <div class="slider-wrapper">
                        <div class="slider-track" id="sliderTrack"></div>
                    </div>
                </div>

                @if(app('event') && (app('event')->start_time || app('event')->end_time))
                    <div class="info-bar">
                        <div class="info-item">
                            <i class="fa-regular fa-calendar"></i>
                            <div class="info-text">
                                <span>
                                    @if(app('event')->start_time)
                                        {{ \Carbon\Carbon::parse(app('event')->start_time)->format('j F, Y') }}
                                    @endif
                                    @if(app('event')->end_time)
                                        @if(app('event')->start_time) - @endif
                                        {{ \Carbon\Carbon::parse(app('event')->end_time)->format('j F, Y') }}
                                    @endif
                                </span>
                                <small>Summit Date</small>
                            </div>
                        </div>

                        @if(app('event')->start_time)
                            <div class="info-item">
                                <i class="fa-regular fa-clock"></i>
                                <div class="info-text">
                                <span>
                                    {{ \Carbon\Carbon::parse(app('event')->start_time)->format('h:i A') }} Onwards
                                </span>
                                    <small>Reporting</small>
                                </div>
                            </div>
                        @endif

                        <div class="info-item no-border registration-info-item">
                            <div class="registration-info-content">
                                <div class="registration-status-row">
                                    <i class="fa-solid fa-hourglass-end"></i>
                                    <span>Registration Open</span>
                                    @if(app('event')->start_time)
                                        <div class="home-event-countdown" id="homeEventCountdown"
                                             data-start="{{ app('event')->start_time->toIso8601String() }}"
                                             aria-label="Time remaining until the event starts">
                                            @foreach(['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Mins', 'seconds' => 'Secs'] as $unit => $label)
                                                <span class="home-countdown-unit">
                                                    <strong data-home-countdown="{{ $unit }}">00</strong>
                                                    <small>{{ $label }}</small>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-gold mobile-register-button open-register-modal">
                                    Register
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if(isset($contents['about-us']) && filled(strip_tags((string) $contents['about-us']->content)))
            <div class="content-grid">
                    <div class="about-section" id="about-us">
                        <h3>{!! $contents['about-us']->title !!}</h3>
                        <div>{!! $contents['about-us']->content !!}</div>
                    </div>

{{--                <div class="sidebar">--}}
{{--                    <div class="sidebar-card organizer-card-layout">--}}
{{--                        <div class="org-top-row">--}}
{{--                            <span class="org-label">Powered By</span>--}}
{{--                            <img src="{{asset('website/images/organizer-logo.png')}}"--}}
{{--                                 alt="CodeMasters Logo"--}}
{{--                                 class="org-logo-small"/>--}}
{{--                        </div>--}}

{{--                        <div class="org-divider"></div>--}}

{{--                        <div class="org-name-row">--}}
{{--                            <h4>CodeMasters Foundation</h4>--}}
{{--                        </div>--}}

{{--                        <div class="org-divider"></div>--}}

{{--                        <div class="org-social-row">--}}
{{--                            <span class="org-label">Join Community</span>--}}
{{--                            <div class="org-social-icons">--}}
{{--                                <a href="#" class="social-btn youtube"><i class="fa-brands fa-youtube"></i></a>--}}
{{--                                <a href="#" class="social-btn linkedin"><i class="fa-brands fa-linkedin-in"></i></a>--}}
{{--                                <a href="#" class="social-btn instagram"><i class="fa-brands fa-instagram"></i></a>--}}
{{--                                <a href="#" class="social-btn facebook"><i class="fa-brands fa-github"></i></a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="sidebar-card venue-card-layout">--}}
{{--                        <div class="venue-top-row">--}}
{{--                            <span class="venue-label">Tech Hub Location</span>--}}
{{--                        </div>--}}

{{--                        <div class="venue-divider"></div>--}}

{{--                        <div class="venue-address-row">--}}
{{--                            <i class="fa-solid fa-location-dot"></i>--}}
{{--                            <p>Gujarat Tech Park, InfoCity, Gandhinagar, Gujarat</p>--}}
{{--                        </div>--}}

{{--                        <div class="venue-map-container">--}}
{{--                            <div class="map-frame">--}}
{{--                                <iframe--}}
{{--                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3667.3187229380155!2d72.63344267408078!3d23.19505250987198!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395c2a39d06fe04f%3A0xa5c52a12a1286368!2sInfocity%20-%20The%20Global%20IT%20Park%20in%20Gujarat!5e0!3m2!1sen!2sin!4v1766053887093!5m2!1sen!2sin"--}}
{{--                                    width="100%" height="100%" allowfullscreen=""--}}
{{--                                    loading="lazy"></iframe>--}}
{{--                            </div>--}}
{{--                            <button class="btn btn-gold full-width">Locate Venue</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
            @endif

            @if($speakers->count()>0)
                <div class="speakers-section" id="speakers">
                    <h3>Speakers</h3>
                    <div class="speakers-grid">
                        @forelse($speakers as $speaker)
                            <div class="speaker-profile-card">
                                <div class="sp-img-container">
                                    <img src="{{ $speaker->image_url }}" alt="{{ $speaker->name }}">
                                </div>
                                <h4>{{ $speaker->name }}</h4>
                                <p>{{ $speaker->line1 }}</p>
                                <p>{{ $speaker->line2 }}</p>
                                <p>{{ $speaker->line3 }}</p>
                            </div>
                        @empty
                            <p>No speakers available.</p>
                        @endforelse
                    </div>
                </div>
            @endif
            @if($brands->count()>0)
                <div class="sponsors-section" id="brands">
                    <h3>Brands</h3>
                    <div class="sponsors-grid">
                        @forelse($brands as $brand)
                            <div class="sponsor-card">
                                <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}">
                            </div>
                        @empty
                            <p>No brands available.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </main>

    <div id="loginModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-title-row">
                <span class="close-modal-btn">×</span>
                <h2>Welcome</h2>
            </div>
            <form method="POST" action="{{ event_route('login') }}" id="loginForm">
                @csrf
                @foreach($login_fields as $field)
                    @php
                        $fieldIcons = [
                            'email' => 'fa-solid fa-envelope', 'password' => 'fa-solid fa-lock',
                            'mobile_number' => 'fa-solid fa-phone', 'alternative_mobile_number' => 'fa-solid fa-phone',
                            'first_name' => 'fa-solid fa-user', 'last_name' => 'fa-solid fa-user',
                            'country' => 'fa-solid fa-earth-asia', 'state' => 'fa-solid fa-map',
                            'city' => 'fa-solid fa-city', 'gender' => 'fa-solid fa-venus-mars',
                        ];
                        $loginIcon = $fieldIcons[$field->field_name] ?? 'fa-solid fa-user';
                    @endphp
                    <div class="email-input-group">
                        <div class="icon-box">
                            <i class="{{ $loginIcon }}"></i>
                        </div>
                        <input type="{{ $field->field_type }}"
                               name="{{ $field->field_name }}"
                               placeholder="{{ $field->label }}"
                               data-field="{{ $field->field_name }}"
                               data-label="{{ $field->label }}"
                               data-is-required="1"/>
                    </div>
                @endforeach
                <button type="submit" class="btn btn-gold full-width" id="btnLogin">Login</button>
            </form>
        </div>
    </div>

    <div id="registerModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-title-row">
                <h2>Registration</h2>
                <span class="close-modal-btn close-register-btn">×</span>
            </div>
            <form method="POST" action="{{ event_route('register') }}" id="registerForm"
                  autocomplete="off">
                @csrf
                @if(!$has_country_field && $default_country)
                    <input type="hidden" id="country" data-id="{{ $default_country->id }}">
                @endif
                @if(!$has_state_field && $default_state)
                    <input type="hidden" id="state" data-id="{{ $default_state->id }}">
                @endif
                <div class="row">
                    @foreach($register_fields as $field)

                        @php
                            $col_class = $field->html_class === 'full' ? 'col-md-12' : 'col-md-6';

                            $options = is_array(json_decode($field->input_value, true))
                             ? json_decode($field->input_value, true)
                             : [];

                            $fieldIcons = [
                                'email' => 'fa-solid fa-envelope', 'password' => 'fa-solid fa-lock',
                                'mobile_number' => 'fa-solid fa-phone', 'alternative_mobile_number' => 'fa-solid fa-phone',
                                'first_name' => 'fa-solid fa-user', 'last_name' => 'fa-solid fa-user',
                                'country' => 'fa-solid fa-earth-asia', 'state' => 'fa-solid fa-map',
                                'city' => 'fa-solid fa-city', 'gender' => 'fa-solid fa-venus-mars',
                                'address' => 'fa-solid fa-location-dot', 'date_of_birth' => 'fa-solid fa-calendar',
                            ];
                            $icon_class = $field->icon ?: ($fieldIcons[$field->field_name] ?? 'fa-solid fa-pen');
                            $input_value = json_decode($field->input_value,true);
                            $source = [];
                            $source_value = '';
                            $source_label = '';
                            if (json_last_error() !== JSON_ERROR_NONE){
                                $input_value = $field->input_value;
                            }else{
                                if(isset($input_value['source']) && !empty($input_value['source'])){
                                    $source_value = isset($input_value['value'])?$input_value['value']:'id';
                                    $source_label = isset($input_value['label'])?$input_value['label']:'name';
                                    if($field->field_name !== 'city')
                                    $source = \Illuminate\Support\Facades\DB::table($input_value['source'])->select('*')->get();
                                }
                            }

                            if ($field->field_name === 'country') {
                                $source = $countries;
                            } elseif ($field->field_name === 'state') {
                                $source = $initial_states;
                            } elseif ($field->field_name === 'city') {
                                $source = $initial_cities;
                            }
                        @endphp

                        <div class="{{ $col_class }} col-12" data-register-field="{{ $field->field_name }}">
                            <div class="email-input-group mb-3">
                                <div class="icon-box">
                                    <i class="{{ $icon_class }}"></i>
                                </div>
                                @switch($field->attribute_data->type)
                                    @case('text')
                                        <input type="text" data-is-required="{{ $field->is_required }}"
                                               name="{{ $field->field_name }}" data-label="{{ $field->label }}"
                                               placeholder="{{ $field->label }}"
                                               value="{{ old($field->field_name) }}" class="form-control"
                                               autocomplete="nope">
                                        @break('text')
                                    @case('textarea')
                                        <textarea name="{{ $field->field_name }}"
                                                  data-is-required="{{ $field->is_required }}"
                                                  data-label="{{ $field->label }}" class="form-control"
                                                  autocomplete="nope"
                                                  placeholder="{{ $field->label }}">{{ old($field->field_name) }}</textarea>
                                        @break('textarea')
                                    @case('password')
                                        <input type="password" data-is-required="{{ $field->is_required }}"
                                               name="{{ $field->field_name }}" data-label="{{ $field->label }}"
                                               placeholder="{{ $field->label }}"
                                               value="{{ old($field->field_name) }}" class="form-control"
                                               autocomplete="nope">
                                        @break('password')
                                    @case('select')
                                        <select
                                            name="{{$field->field_name}}"
                                            id="{{$field->field_name}}"
                                            class="form-select select2"
                                            data-label="{{ $field->label }}"
                                            data-is-required="{{ $field->is_required }}">
                                            <option value="">Select {{ $field->label }}</option>
                                            @foreach($source as $key=>$value)
                                                @php
                                                    $optionId = $value->{$source_value ?: 'id'} ?? $value->id ?? '';
                                                    $optionLabel = $value->{$source_label ?: 'name'} ?? $value->name ?? '';
                                                    $selectedValue = old($field->field_name);
                                                    if ($field->field_name === 'country' && !$selectedValue) {
                                                        $selectedValue = $default_country?->name;
                                                    }
                                                @endphp
                                                <option
                                                    value="{{ $optionLabel }}" data-id="{{ $optionId }}"
                                                    @selected($selectedValue === $optionLabel)>{{ $optionLabel }}</option>
                                            @endforeach
                                            @if(collect($source)->isEmpty())
                                                @foreach($options as $key => $option)
                                                    @php
                                                        $optionValue = is_array($option) ? ($option['value'] ?? $key) : $key;
                                                        $optionLabel = is_array($option) ? ($option['label'] ?? $optionValue) : $option;
                                                    @endphp
                                                    <option value="{{ $optionValue }}" @selected(old($field->field_name) == $optionValue)>{{ $optionLabel }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @break('select')
                                    @case('checkbox')
                                        @foreach($options as $k => $v)
                                            <label>
                                                <input data-is-required="{{ $field->is_required }}"
                                                       data-label="{{ $field->label }}"
                                                       type="checkbox" name="{{ $field->field_name }}[]"
                                                       value="{{ $k }}">
                                                {{ $v }}
                                            </label>
                                        @endforeach
                                        @break('checkbox')
                                    @case('date')
                                        <input data-is-required="{{ $field->is_required }}"
                                               data-label="{{ $field->label }}"
                                               type="date" name="{{ $field->field_name }}" class="form-control">
                                        @break('date')
                                    @case('file')
                                        <input data-is-required="{{ $field->is_required }}"
                                               data-label="{{ $field->label }}"
                                               type="file" name="{{ $field->field_name }}" class="form-control">
                                        @break('file')
                                    @case('radio')
                                        @foreach($options as $k => $v)
                                            <label>
                                                <input data-is-required="{{ $field->is_required }}"
                                                       data-label="{{ $field->label }}"
                                                       type="radio" name="{{ $field->field_name }}"
                                                       value="{{ $k }}">{{ $v }}
                                            </label>
                                        @endforeach
                                        @break('radio')
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-gold full-width mt-3" id="btnRegister">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function () {
                const homeCountdown = document.getElementById('homeEventCountdown');
                if (homeCountdown) {
                    const startsAt = new Date(homeCountdown.dataset.start).getTime();
                    const countdownFields = Object.fromEntries(
                        [...homeCountdown.querySelectorAll('[data-home-countdown]')]
                            .map(field => [field.dataset.homeCountdown, field])
                    );

                    const updateHomeCountdown = () => {
                        if (!Number.isFinite(startsAt)) return;

                        const distance = Math.max(0, startsAt - Date.now());
                        const values = {
                            days: Math.floor(distance / 86400000),
                            hours: Math.floor((distance % 86400000) / 3600000),
                            minutes: Math.floor((distance % 3600000) / 60000),
                            seconds: Math.floor((distance % 60000) / 1000),
                        };

                        Object.entries(values).forEach(([unit, value]) => {
                            countdownFields[unit].textContent = String(value).padStart(2, '0');
                        });

                        if (distance === 0) {
                            homeCountdown.innerHTML = '<span class="home-countdown-live">Starting now</span>';
                            window.clearInterval(homeCountdownTimer);
                        }
                    };

                    const homeCountdownTimer = window.setInterval(updateHomeCountdown, 1000);
                    updateHomeCountdown();
                }

                const loginForm = $("#loginForm");
                const registerForm = $("#registerForm");
                const statesUrlTemplate = @json(event_route('states', ['country' => '__COUNTRY_ID__']));
                const citiesUrlTemplate = @json(event_route('cities', ['state' => '__STATE_ID__']));
                const $country = registerForm.find('#country');
                const $state = registerForm.find('#state');
                const $city = registerForm.find('#city');

                const selectedLocationId = $field => $field.is('select')
                    ? $field.find(':selected').data('id')
                    : $field.data('id');

                const fillLocationOptions = ($select, items, label) => {
                    if (!$select.is('select')) return;
                    $select.empty().append(new Option(`Select ${label}`, ''));
                    (items || []).forEach(item => {
                        const option = new Option(item.name, item.name);
                        option.dataset.id = item.id;
                        $select.append(option);
                    });
                    $select.val('').trigger('change.select2');
                };

                $country.on('change', function () {
                    if (!$state.is('select')) return;
                    const countryId = selectedLocationId($country);
                    fillLocationOptions($state, [], 'State');
                    fillLocationOptions($city, [], 'City');
                    if (!countryId) return;

                    $.getJSON(statesUrlTemplate.replace('__COUNTRY_ID__', countryId))
                        .done(items => fillLocationOptions($state, items, 'State'))
                        .fail(() => toastr.error('Unable to load states.'));
                });

                $state.on('change', function () {
                    if (!$city.is('select')) return;
                    const stateId = selectedLocationId($state);
                    fillLocationOptions($city, [], 'City');
                    if (!stateId) return;

                    $.getJSON(citiesUrlTemplate.replace('__STATE_ID__', stateId))
                        .done(items => fillLocationOptions($city, items, 'City'))
                        .fail(() => toastr.error('Unable to load cities.'));
                });

                loginForm.validate({
                    errorElement: "div",
                    errorClass: "error-text",
                    errorPlacement: function (error, element) {
                        error.insertAfter(element.closest(".email-input-group"));
                    },
                    highlight: el => $(el).addClass("is-invalid"),
                    unhighlight: el => $(el).removeClass("is-invalid")
                });

                loginForm.find("input").each(function () {
                    const $el = $(this);
                    const label = $el.data("label") || "This field";
                    const type = $el.attr("type");
                    const name = $el.attr("name");

                    let rules = {}, messages = {};

                    if ($el.data("is-required") == 1) {
                        rules.required = true;
                        messages.required = `${label} is required`;
                    }

                    if (type === "email" || name === "email") {
                        rules.email = true;
                        messages.email = "Enter valid email";
                    }

                    if (type === "tel" || name === "mobile_number" || name === "phone") {
                        rules.digits = true;
                        rules.minlength = 10;
                        rules.maxlength = 10;

                        messages.digits = "Only numbers allowed";
                        messages.minlength = "Must be 10 digits";
                        messages.maxlength = "Must be 10 digits";
                    }

                    if (Object.keys(rules).length) {
                        $el.rules("add", {...rules, messages});
                    }
                });

                loginForm.on("submit", function (event) {
                    event.preventDefault();

                    if (!loginForm.valid()) return;

                    const formData = new FormData(loginForm[0]);

                    $.ajax({
                        url: "{{ event_route('login') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",

                        beforeSend: function () {
                            $("#btnLogin").prop("disabled", true).text("Please wait...");
                        },

                        success: function (res) {
                            if (res.status) {
                                toastr.success(res.message || "Login successful");
                                window.location.href = '{{ event_route('dashboard') }}';
                            } else {
                                toastr.error(res.message || "Login failed");
                            }
                        },

                        error: function (xhr) {
                            const res = xhr.responseJSON;

                            if (xhr.status === 422 && res?.errors) {
                                $.each(res.errors, function (field, messages) {
                                    toastr.error(messages[0]);
                                });
                            } else if (xhr.status === 401 && res?.type === 'registration_required') {
                                toastr.info(res.message || "Please complete your registration");
                                closeLoginModal();
                                openRegisterModal(res.registration_data || {});
                            } else if (xhr.status === 401) {
                                toastr.error(res.message || "Invalid credentials");
                            } else {
                                toastr.error("Something went wrong");
                            }
                        },
                        complete: function () {
                            $("#btnLogin").prop("disabled", false).text("Login");
                        }
                    });
                });


                registerForm.validate({
                    errorElement: "div",
                    errorClass: "error-text",
                    errorPlacement: function (error, element) {
                        error.insertAfter(element.closest(".email-input-group"));
                    },
                    highlight: function (el) {
                        $(el).addClass("is-invalid");
                    },
                    unhighlight: function (el) {
                        $(el).removeClass("is-invalid");
                    }
                });

                registerForm.find("input, select, textarea").each(function () {
                    const $input = $(this);
                    const label = $input.data("label") || "This field";
                    const type = $input.attr("type");
                    const name = $input.attr("name");
                    let rules = {}, messages = {};

                    if ($input.data("is-required") == 1) {
                        rules.required = true;
                        messages.required = label + " is required";
                    }
                    if (name === "email") {
                        rules.email = true;
                        messages.email = "Please enter a valid email address";
                    }
                    if (type === "tel" || name === "mobile_number") {
                        rules.digits = true;
                        rules.minlength = 10;
                        rules.maxlength = 10;
                        messages.digits = "Only numbers are allowed";
                        messages.minlength = "Mobile number must be 10 digits";
                        messages.maxlength = "Mobile number must be 10 digits";
                    }
                    if (name === "password") {
                        rules.minlength = 6;
                        messages.minlength = label + " must be at least 6 characters";
                    }
                    if (Object.keys(rules).length > 0) {
                        $input.rules("add", {...rules, messages});
                    }
                });

                $("#btnRegister").on("click", function () {

                    if (!registerForm.valid()) return;

                    const formData = new FormData(registerForm[0]);

                    $.ajax({
                        url: "{{ event_route('register') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",

                        beforeSend: function () {
                            $("#btnRegister").prop("disabled", true).text("Please wait...");
                        },

                        success: function (res) {
                            if (res.status) {
                                toastr.success(res.message || "Register successful");
                                window.location.href = '{{ event_route('dashboard') }}';
                            } else {
                                toastr.error(res.message || "Register failed");
                            }
                        },

                        error: function (xhr) {
                            const res = xhr.responseJSON;

                            if (xhr.status === 422 && res?.errors) {
                                $.each(res.errors, function (field, messages) {
                                    toastr.error(messages[0]);
                                });
                            } else if (xhr.status === 401) {
                                toastr.error(res.message || "Invalid credentials");
                            } else {
                                toastr.error("Something went wrong");
                            }
                        },
                        complete: function () {
                            $("#btnRegister").prop("disabled", false).text("Register");
                        }
                    });
                });

            });
            window.sliderData = @json($banners??[]);
            const loginModal = document.getElementById("loginModal");
            const registerModal = document.getElementById("registerModal");
            const loginBtn = document.getElementById("openLoginModal");
            const registerButtons = document.querySelectorAll(".open-register-modal");
            const closeLoginBtn = document.querySelector(".close-modal-btn");
            const closeRegisterBtn = document.querySelector(".close-register-btn");

            function openLoginModal() {
                if (loginModal) {
                    loginModal.style.display = "flex";
                    body.style.overflow = "hidden";
                }
            }

            function closeLoginModal() {
                if (loginModal) {
                    loginModal.style.display = "none";
                    body.style.overflow = "auto";
                }
            }

            function resetTransferredRegistrationFields() {
                if (!registerModal) return;

                registerModal.querySelectorAll('[data-login-prefilled="true"]').forEach(function (field) {
                    const input = field.querySelector('input, select, textarea');
                    if (input) input.value = '';
                    field.removeAttribute('data-login-prefilled');
                });
            }

            function openRegisterModal(registrationData = null) {
                if (registerModal) {
                    resetTransferredRegistrationFields();

                    if (registrationData && !(registrationData instanceof Event)) {
                        Object.entries(registrationData).forEach(function ([name, value]) {
                            const input = registerModal.querySelector(`[name="${CSS.escape(name)}"]`);
                            const field = registerModal.querySelector(`[data-register-field="${CSS.escape(name)}"]`);
                            if (!input || !field) return;

                            input.value = value;
                            field.setAttribute('data-login-prefilled', 'true');
                        });
                    }

                    registerModal.style.display = "flex";
                    body.style.overflow = "hidden";
                }
            }

            function closeRegisterModal() {
                if (registerModal) {
                    registerModal.style.display = "none";
                    body.style.overflow = "auto";
                }
            }

            if (loginBtn) loginBtn.addEventListener("click", openLoginModal);
            registerButtons.forEach(button => button.addEventListener("click", () => openRegisterModal()));
            if (closeLoginBtn) closeLoginBtn.addEventListener("click", closeLoginModal);
            if (closeRegisterBtn) closeRegisterBtn.addEventListener("click", closeRegisterModal);
            window.addEventListener("click", function (event) {
                if (event.target === loginModal) closeLoginModal();
                if (event.target === registerModal) closeRegisterModal();
            });
            $(document).ready(function () {
                const $track = $("#sliderTrack");
                const sliderData = window.sliderData || [];
                let slideIndex = 0;
                let slideInterval;
                const slideDuration = 6000;

                if ($track.length === 0 || sliderData.length === 0) return;

                function initSlider() {
                    $track.empty();
                    $.each(sliderData, function (index, item) {
                        let mediaElement = '';
                        if (item.type === 'image') {
                            mediaElement = `<img src="${item.src}" alt="Event Banner">`;
                        } else if (item.type === 'video') {
                            mediaElement = `<video poster="${item.poster || ''}" muted playsinline loop>
                                    <source src="${item.src}">
                                </video>`;
                        }
                        $track.append(`<div class="slide">${mediaElement}</div>`);
                    });
                    updateSlider();
                    startAutoSlide();
                }

                function updateSlider() {
                    $track.css("transform", `translateX(-${slideIndex * 100}%)`);
                    updateBackground(slideIndex);
                    $(".slide video").each(function () {
                        this.pause();
                        this.currentTime = 0;
                    });
                    const $activeVideo = $(".slide").eq(slideIndex).find("video");
                    if ($activeVideo.length > 0) {
                        const p = $activeVideo.get(0).play();
                        if (p !== undefined) p.catch(e => console.log("Auto-play blocked:", e));
                    }
                }

                function updateBackground(index) {
                    const data = sliderData[index];
                    const $bgImg = $("#bgImage");
                    const $bgVid = $("#bgVideo");
                    $(".bg-media").removeClass("active");
                    if (data.type === 'image') {
                        $bgVid.trigger('pause');
                        $bgImg.attr("src", data.src).addClass("active");
                    } else if (data.type === 'video') {
                        $bgVid.attr("src", data.src).addClass("active");
                        const v = $bgVid.get(0);
                        v.load();
                        const p = v.play();
                        if (p !== undefined) p.catch(e => console.log("Bg Video Auto-play blocked:", e));
                    }
                }

                function nextSlide() {
                    slideIndex = (slideIndex + 1) % sliderData.length;
                    updateSlider();
                }

                function startAutoSlide() {
                    if (slideInterval) clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, slideDuration);
                }

                initSlider();
            });
        </script>
    @endpush

@endsection
