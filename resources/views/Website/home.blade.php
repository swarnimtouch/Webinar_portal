@extends('layouts.website')

@section('title', 'TechNova IT Summit 2025')

@push('styles')
    <style>
        .error-text {
            color: #d93025;
            font-size: 13px;
            margin-top: 4px;
            display: none;
            align-items: center;
            gap: 6px;
        }

        .error-text i {
            font-size: 12px;
        }

        .is-invalid {
            border-color: #d93025 !important;
            background-color: #fff5f5;
        }

        .is-invalid:focus {
            border-color: #d93025 !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 48, 37, 0.25);
        }

        /* Email Input Group Styling */
        .email-input-group {
            margin-bottom: 1.2rem;
        }

        .email-input-group input {
            transition: all 0.3s ease;
        }

        /* Animation for error messages */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-text {
            animation: slideDown 0.3s ease;
        }
    </style>
@endpush

@section('body')
    <nav class="navbar">
        <div class="logo">
            <h2>TECH<span>NOVA</span></h2>
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
                        <div class="error-text js-error"></div>
                    </div>
                @endforeach
                @error($field->field_name)
                <div class="error-text" style="display:flex">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </div>
                @enderror

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
                        $inputName = $field->field_name === 'mobile_number' ? 'mobile' : $field->field_name;
                    @endphp
                    <div class="email-input-group">
                        <div class="icon-box">
                            <i class="{{ $field->icon ?? 'fa-solid fa-user' }}"></i>
                        </div>
                        <input type="{{ $field->field_type }}"
                               name="{{ $inputName }}"
                               placeholder="{{ $field->label }}"
                               data-field="{{ $field->field_name }}"
                               data-label="{{ $field->label }}"
                               data-is-required="{{ $field->is_required }}"
                               class="@error($inputName) is-invalid @enderror"
                               value="{{ old($inputName) }}" />

                        @error($inputName)
                        <div class="error-text" style="display: flex;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}
                        </div>
                        @enderror

                        <div class="error-text js-error"></div>
                    </div>
                @endforeach
                <button type="submit" class="btn btn-gold full-width">Register</button>
            </form>
        </div>
    </div>

    @push('scripts')
        {{-- Include FormValidator --}}



        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = document.getElementById('registerModal');
                    if (modal) {
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    }
                });
            </script>
        @endif

    @endpush

@endsection
