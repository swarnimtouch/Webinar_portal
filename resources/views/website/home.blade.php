@extends('layouts.website')
@section('body')
    <main>
        <div class="container">
            <div class="header-section">
                <h1>{{$homesetting->title ?? 'Webinar Portal'}}</h1>
                <button class="btn btn-gold" id="openRegisterModal">Register</button>
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

                <div class="info-bar">
                    <div class="info-item">
                        <i class="fa-regular fa-calendar"></i>
                        <div class="info-text">
                            <span>
                                {{ $homesetting?->event_start_time
                                    ? \Carbon\Carbon::parse($homesetting->event_start_time)->format('j F, Y')
                                    : 'TBD' }}
                                -
                                {{ $homesetting?->event_end_time
                                    ? \Carbon\Carbon::parse($homesetting->event_end_time)->format('j F, Y')
                                    : 'TBD' }}
                            </span>

                            <small>Summit Date</small>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fa-regular fa-clock"></i>
                        <div class="info-text">
                            <span>
                                {{ $homesetting?->event_start_time
                                    ? \Carbon\Carbon::parse($homesetting->event_start_time)->format('H:i') . ' Onwards'
                                    : 'Time TBD' }}
                            </span>

                            <small>Reporting</small>
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
                            <img src="{{asset('website/images/organizer-logo.png')}}"
                                 alt="CodeMasters Logo"
                                 class="org-logo-small"/>
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
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3667.3187229380155!2d72.63344267408078!3d23.19505250987198!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395c2a39d06fe04f%3A0xa5c52a12a1286368!2sInfocity%20-%20The%20Global%20IT%20Park%20in%20Gujarat!5e0!3m2!1sen!2sin!4v1766053887093!5m2!1sen!2sin"
                                    width="100%" height="100%" allowfullscreen=""
                                    loading="lazy"></iframe>
                            </div>
                            <button class="btn btn-gold full-width">Locate Venue</button>
                        </div>
                    </div>
                </div>
            </div>

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
                               data-is-required="1"/>
                    </div>
                @endforeach
                <button type="submit" class="btn btn-gold full-width">Login</button>
            </form>
        </div>
    </div>

    {{-- Register Modal --}}
    <div id="registerModal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal-btn close-register-btn">×</span>
            <h2>Doctor Registration</h2>
            <form method="POST" action="{{ route('website.register.submit') }}" id="registerForm">
                @csrf
                @foreach($registerFields as $field)

                    @php
                        $options = is_array(json_decode($field->input_value, true))
                          ? json_decode($field->input_value, true)
                          : [];

                        $iconClass = $field->icon;

                        if (empty($iconClass)) {
                            $fieldName = strtolower($field->field_name);

                            if (str_contains($fieldName, 'name')) {
                                $iconClass = 'fa-solid fa-user';
                            } elseif (str_contains($fieldName, 'email')) {
                                $iconClass = 'fa-solid fa-envelope';
                            } elseif (str_contains($fieldName, 'mobile') || str_contains($fieldName, 'phone')) {
                                $iconClass = 'fa-solid fa-phone';
                            } elseif (str_contains($fieldName, 'password')) {
                                $iconClass = 'fa-solid fa-lock';
                            } elseif ($fieldName === 'country') {
                                $iconClass = 'fa-solid fa-globe';
                            } elseif ($fieldName === 'state') {
                                $iconClass = 'fa-solid fa-map-location-dot';
                            } elseif ($fieldName === 'city') {
                                $iconClass = 'fa-solid fa-city';
                            } elseif ($field->attribute_id == 5) {
                                $iconClass = 'fa-solid fa-calendar-days';
                            } elseif ($field->attribute_id == 6) {
                                $iconClass = 'fa-solid fa-file-arrow-up';
                            } else {
                                $iconClass = 'fa-solid fa-pen';
                            }
                        }
                    @endphp

                    <div class="email-input-group mb-3">
                        <div class="icon-box">
                            <i class="{{ $iconClass }}"></i>
                        </div>

                        @if($field->attribute_id == 1)
                            <input type="text" data-is-required="{{ $field->is_required }}"
                                   name="{{ $field->field_name }}" data-label="{{ $field->label }}"
                                   placeholder="{{ $field->label }}"
                                   value="{{ old($field->field_name) }}" class="form-control">

                        @elseif($field->attribute_id == 2)
                            <textarea name="{{ $field->field_name }}" data-is-required="{{ $field->is_required }}"
                                      data-label="{{ $field->label }}" class="form-control"
                                      placeholder="{{ $field->label }}">{{ old($field->field_name) }}</textarea>
                        @elseif($field->attribute_id == 7)
                            <input type="password" data-is-required="{{ $field->is_required }}"
                                   name="{{ $field->field_name }}" data-label="{{ $field->label }}"
                                   placeholder="{{ $field->label }}"
                                   value="{{ old($field->field_name) }}" class="form-control">

                        @elseif(in_array($field->attribute_id, [3,13]))

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

                        @elseif($field->attribute_id == 4)
                            <select data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                    name="{{ $field->field_name }}[]" multiple class="form-control">
                                @foreach($options as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>

                        @elseif($field->attribute_id == 5)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                   type="date" name="{{ $field->field_name }}" class="form-control">

                        @elseif($field->attribute_id == 6)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                   type="file" name="{{ $field->field_name }}" class="form-control">

                        @elseif($field->attribute_id == 7)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                   type="password" name="{{ $field->field_name }}" class="form-control">

                        @elseif($field->attribute_id == 9)
                            @forelse($options as $k => $v)
                                <label>
                                    <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                           type="checkbox" name="{{ $field->field_name }}[]" value="{{ $k }}">
                                    {{ $v }}
                                </label>
                            @empty
                                <p class="text-danger">No options available</p>
                            @endforelse

                        @elseif($field->attribute_id == 10)
                            <label>
                                <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                       type="checkbox" name="{{ $field->field_name }}"> {{ $field->label }}
                            </label>

                        @elseif($field->attribute_id == 11)
                            <label class="me-3">
                                {{ $field->label }}
                            </label>
                            @forelse($options as $k => $v)

                                <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                       type="radio" name="{{ $field->field_name }}" value="{{ $k }}">
                                {{ $v }}
                            @empty
                                <p class="text-danger">No options configured</p>
                            @endforelse

                        @elseif($field->attribute_id == 12)
                            <input data-is-required="{{ $field->is_required }}" data-label="{{ $field->label }}"
                                   type="datetime-local" name="{{ $field->field_name }}" class="form-control">

                        @endif
                    </div>
                @endforeach
                <button type="submit" class="btn btn-gold full-width">Register</button>
            </form>
        </div>
    </div>

    @push('scripts')
        @if(session('toast_error'))
            <script>toastr.error("{{ session('toast_error') }}");</script>
        @endif

        <script>
            window.sliderData = @json($sliderData);
            window._openLoginModal = {{ session('open_login_modal')    ? 'true' : 'false' }};
            window._openRegisterModal = {{ session('open_register_modal') ? 'true' : 'false' }};
        </script>
    @endpush

@endsection
