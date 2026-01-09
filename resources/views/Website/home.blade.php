@extends('layouts.website')

@section('title', 'TechNova IT Summit 2025')

@push('styles')
@endpush

@section('body')
    @php
        $siteLogo = siteSetting('site_logo');
        $siteName = siteSetting('site_name', config('app.name'));
    @endphp


    <nav class="navbar">
        <div class="logo">
            @if($siteLogo)
                <img src="{{ asset('storage/site_settings/' . $siteLogo) }}"
                     alt="{{ $siteName }}"
                     class="site-logo-img"
                     style="width:50px; height:50px; object-fit:contain;">
            @else
                <h2>TECH<span>NOVA</span></h2>
            @endif


        </div>
        <div class="nav-links">
            <a href="#">About Event</a>
            <a href="#">Speakers</a>
            <a href="#">Brands</a>
        </div>
        <button class="btn btn-gold" id="openLoginModal">Login</button>
    </nav>

    <main>
        <div class="container">
            <div class="header-section">
                <h1>TechNova IT Summit 2025 by CodeMasters Hub</h1>
                <button class="btn btn-gold" id="openRegisterModal">Register</button>
            </div>

            <div class="intro-card">
                <div class="hero-banner">
                    <div class="slider-wrapper">
                        <div class="slider-track" id="sliderTrack"></div>
                    </div>
                </div>

                <div class="info-bar">
                    <div class="info-item">
                        <i class="fa-regular fa-calendar"></i>
                        <div class="info-text">
                            <span>24 Dec, 2025 - 25 Dec, 2025</span>
                            <small>Summit Date</small>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fa-regular fa-clock"></i>
                        <div class="info-text">
                            <span>09:00 AM Onwards</span>
                            <small>Reporting</small>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div class="info-text">
                            <span>Gujarat Tech Park, InfoCity, Gandhinagar</span>
                            <small>Tech Zone</small>
                        </div>
                    </div>
                    <div class="info-item no-border">
                        <i class="fa-solid fa-hourglass-end"></i>
                        <div class="info-text">
                            <span>Registration Open</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <div class="about-section">
                    <h3>{!! $contents['about-us']->title ?? 'About Us' !!}</h3>
                    <p>{!! $contents['about-us']->content ?? '' !!}</p>
                </div>

                <div class="sidebar">
                    <div class="sidebar-card organizer-card-layout">
                        <div class="org-top-row">
                            <span class="org-label">Powered By</span>
                            <img src="{{asset('assets/Website/images/organizer-logo.png')}}"
                                 alt="CodeMasters Logo"
                                 class="org-logo-small" />
                        </div>

                        <div class="org-divider"></div>

                        <div class="org-name-row">
                            <h4>CodeMasters Foundation</h4>
                        </div>

                        <div class="org-divider"></div>

                        <div class="org-social-row">
                            <span class="org-label">Join Community</span>
                            <div class="org-social-icons">
                                <a href="#" class="social-btn youtube"><i class="fa-brands fa-youtube"></i></a>
                                <a href="#" class="social-btn linkedin"><i class="fa-brands fa-linkedin-in"></i></a>
                                <a href="#" class="social-btn instagram"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" class="social-btn facebook"><i class="fa-brands fa-github"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card venue-card-layout">
                        <div class="venue-top-row">
                            <span class="venue-label">Tech Hub Location</span>
                        </div>

                        <div class="venue-divider"></div>

                        <div class="venue-address-row">
                            <i class="fa-solid fa-location-dot"></i>
                            <p>Gujarat Tech Park, InfoCity, Gandhinagar, Gujarat</p>
                        </div>

                        <div class="venue-map-container">
                            <div class="map-frame">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3667.3187229380155!2d72.63344267408078!3d23.19505250987198!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395c2a39d06fe04f%3A0xa5c52a12a1286368!2sInfocity%20-%20The%20Global%20IT%20Park%20in%20Gujarat!5e0!3m2!1sen!2sin!4v1766053887093!5m2!1sen!2sin"
                                        width="100%" height="100%" style="border: 0" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                            <button class="btn btn-gold full-width">Locate Venue</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="speakers-section">
                <h3>Speakers</h3>
                <div class="speakers-grid">
                    @forelse($speakers as $speaker)
                        <div class="speaker-profile-card">
                            <div class="sp-img-container">
                                <img src="{{ asset('storage/speakers/' . $speaker->filename) }}"
                                     alt="{{ $speaker->name }}" />
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

            <div class="sponsors-section">
                <h3>Brands</h3>
                <div class="sponsors-grid">
                    @forelse($brands as $brand)
                        <div class="sponsor-card">
                            <img src="{{ asset('storage/brands/' . $brand->filename) }}"
                                 alt="{{ $brand->name ?? 'Brand Logo' }}" />
                        </div>
                    @empty
                        <p>No brands available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    {{-- Login Modal --}}
    <div id="loginModal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal-btn">×</span>
            <h2>Welcome</h2>
            <form method="POST" action="{{ route('website.login.submit') }}" id="loginForm">
                @csrf
                @foreach($loginFields as $field)
                    <div class="email-input-group">
                        <div class="icon-box">
                            <i class="{{ $field->icon ?? 'fa-solid fa-user' }}"></i>
                        </div>
                        <input type="{{ $field->field_type }}"
                               name="{{ $field->field_name }}"
                               placeholder="{{ $field->label }}"
                               data-field="{{ $field->field_name }}"
                               data-label="{{ $field->label }}"
                               data-is-required="1" />
                    </div>
                @endforeach
                <button type="submit" class="btn btn-gold full-width">Login</button>
            </form>
        </div>
    </div>

    {{-- Register Modal --}}
    <div id="registerModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <span class="close-register-btn">×</span>
            <h2>Doctor Registration</h2>
            <form method="POST" action="{{ route('website.register.submit') }}" id="registerForm">
                @csrf
                @foreach($registerFields as $field)

                    @php
                      $options = is_array(json_decode($field->input_value, true))
                        ? json_decode($field->input_value, true)
                        : [];
                    @endphp

                    <div class="email-input-group mb-3">
                        @if($field->input_type == 1)
                            <input type="text" data-is-required="{{ $field->is_required }}" name="{{ $field->field_name }}" data-label="{{ $field->label }}" placeholder="{{ $field->label }}"
                                   value="{{ old($field->field_name) }}" class="form-control">

                        @elseif($field->input_type == 2)
                            <textarea name="{{ $field->field_name }}" data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" class="form-control"
                                      placeholder="{{ $field->label }}">{{ old($field->field_name) }}</textarea>

                        @elseif(in_array($field->input_type, [3,13]))

                            @if($field->field_name === 'country')
                                <select
                                    name="country"
                                    id="country"
                                    class="form-select select2"
                                    data-label="{{ $field->label }}"
                                    data-is-required="{{ $field->is_required }}">
                                    <option value="">Select Country</option>
                                </select>

                            @elseif($field->field_name === 'state')
                                <select
                                    name="state"
                                    id="state"
                                    class="form-select select2"
                                    data-label="{{ $field->label }}"
                                    data-is-required="{{ $field->is_required }}">
                                    <option value="">Select State</option>
                                </select>

                            @elseif($field->field_name === 'city')
                                <select
                                    name="city"
                                    id="city"
                                    class="form-select select2"
                                    data-label="{{ $field->label }}"
                                    data-is-required="{{ $field->is_required }}">
                                    <option value="">Select City</option>
                                </select>

                            @else
                                <select
                                    name="{{ $field->field_name }}"
                                    class="form-select"
                                    data-label="{{ $field->label }}"
                                    data-is-required="{{ $field->is_required }}">
                                    <option value="">Select {{ $field->label }}</option>
                                    @foreach($options as $k => $v)
                                        <option value="{{ $k }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            @endif

                        @elseif($field->input_type == 4)
                            <select data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" name="{{ $field->field_name }}[]" multiple class="form-control">
                                @foreach($options as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>

                        @elseif($field->input_type == 5)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" type="date" name="{{ $field->field_name }}" class="form-control">

                        @elseif($field->input_type == 6)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" type="file" name="{{ $field->field_name }}" class="form-control">

                        @elseif($field->input_type == 7)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" type="password" name="{{ $field->field_name }}" class="form-control">

                        @elseif($field->input_type == 9)
                            @forelse($options as $k => $v)
                                <label>
                                    <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"  type="checkbox" name="{{ $field->field_name }}[]" value="{{ $k }}">
                                    {{ $v }}
                                </label>
                            @empty
                                <p class="text-danger">No options available</p>
                            @endforelse


                        @elseif($field->input_type == 10)
                            <label>
                                <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" type="checkbox" name="{{ $field->field_name }}"> {{ $field->label }}
                            </label>

                        @elseif($field->input_type == 11)
                            <label class="me-3">
                                {{ $field->label }}
                            </label>
                            @forelse($options as $k => $v)

                                    <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" type="radio" name="{{ $field->field_name }}" value="{{ $k }}">
                                    {{ $v }}
                            @empty
                                <p class="text-danger">No options configured</p>
                            @endforelse


                        @elseif($field->input_type == 12)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}" type="datetime-local" name="{{ $field->field_name }}" class="form-control">

                        @endif
                    </div>
                @endforeach
                <button type="submit" class="btn btn-gold full-width">Register</button>
            </form>
        </div>
    </div>

    @push('scripts')
        @if(session('toast_error'))
            <script>
                toastr.error("{{ session('toast_error') }}");
            </script>
        @endif

        @if(session('toast_success'))
            <script>
                toastr.success("{{ session('toast_success') }}");
            </script>
        @endif
        @if(session('open_login_modal'))
            <script>
                $(document).ready(function () {
                    $('#loginModal').css('display', 'flex');
                    $('body').css('overflow', 'hidden');
                });
            </script>
        @endif
        @if(session('open_register_modal'))
            <script>
                $(document).ready(function () {
                    $('#registerModal').css('display', 'flex');
                    $('body').css('overflow', 'hidden');
                });
            </script>
        @endif

        <script>
            window.sliderData = [
                    @foreach($banners as $banner)
                {
                    type: "{{ $banner->type }}",
                    src: "{{ asset('storage/banners/' . $banner->filename) }}"
                }@if(!$loop->last),@endif
                @endforeach
            ];

        </script>

        //validation
        <script>
            $(document).ready(function () {


                const loginValidator = $("#loginForm").validate({
                    errorElement: "div",
                    errorClass: "error-text",

                    errorPlacement: function (error, element) {
                        error.insertAfter(element.closest(".email-input-group"));
                    },

                    highlight: function (element) {
                        $(element).addClass("is-invalid");
                    },

                    unhighlight: function (element) {
                        $(element).removeClass("is-invalid");
                    }
                });



                $("#loginForm input").each(function () {

                    const $input = $(this);
                    const label  = $input.data("label") || "This field";
                    const type   = $input.attr("type");
                    const name   = $input.attr("name");

                    if ($input.data("is-required") == 1) {
                        $input.rules("add", {
                            required: true,
                            messages: {
                                required: label + " is required"
                            }
                        });
                    }

                    if (type === "email" || name === "email") {
                        $input.rules("add", {
                            email: true,
                            messages: {
                                email: "Please enter a valid email address"
                            }
                        });
                    }

                    if (type === "tel" || name === "mobile_number" || name === "phone") {
                        $input.rules("add", {
                            digits: true,
                            minlength: 10,
                            maxlength: 10,
                            messages: {
                                digits: "Only numbers are allowed",
                                minlength: "Mobile number must be 10 digits",
                                maxlength: "Mobile number must be 10 digits"
                            }
                        });
                    }

                });

                const registerValidator = $("#registerForm").validate({
                    errorElement: "div",
                    errorClass: "error-text",

                    errorPlacement: function (error, element) {

                        error.insertAfter(element.closest(".email-input-group"));
                    },

                    highlight: function (element) {
                        $(element).addClass("is-invalid");
                    },

                    unhighlight: function (element) {
                        $(element).removeClass("is-invalid");
                    }
                });


                $("#registerForm").find("input, select, textarea").each(function () {

                    const $input = $(this);
                    const label = $input.data("label") || "This field";
                    const type   = $input.attr("type");
                    const name   = $input.attr("name");

                    let rules = {};
                    let messages = {};

                    if ($input.data("is-required") == 1) {
                        rules.required = true;
                        messages.required = label + " is required";
                    }

                    if (name === "email") {
                        rules.email = true;
                        messages.email = "Please enter a valid email address";
                    }

                    if (name === "tel" || name === "mobile_number") {
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
                        $input.rules("add", {
                            ...rules,
                            messages: messages
                        });
                    }

                });


            });
        </script>

        //country wise state and state wise city
        <script>
            $(document).ready(function () {

                $('.select2').select2({
                    width: '100%',
                    allowClear: true
                });


                $.get('/get-countries', function (countries) {

                    $('#country').append(
                        countries.map(c =>
                            `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`
                        )
                    );

                    if (window.oldCountry) {
                        $('#country').val(window.oldCountry).trigger('change');
                    }
                });


                $('#country').on('change', function () {

                    let countryId = $('#country option:selected').data('id');

                    $('#state').empty().append('<option value="">Select State</option>').trigger('change');
                    $('#city').empty().append('<option value="">Select City</option>').trigger('change');

                    if (!countryId) return;

                    $.get(`/get-states/${countryId}`, function (states) {

                        $('#state').append(
                            states.map(s =>
                                `<option value="${s.name}" data-id="${s.id}">${s.name}</option>`
                            )
                        );

                        if (window.oldState) {
                            $('#state').val(window.oldState).trigger('change');
                        }
                    });
                });


                $('#state').on('change', function () {

                    let stateId = $('#state option:selected').data('id');

                    $('#city').empty().append('<option value="">Select City</option>').trigger('change');

                    if (!stateId) return;

                    $.get(`/get-cities/${stateId}`, function (cities) {

                        $('#city').append(
                            cities.map(c =>
                                `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`
                            )
                        );

                        if (window.oldCity) {
                            $('#city').val(window.oldCity).trigger('change');
                        }
                    });
                });

            });
        </script>

    @endpush

@endsection
