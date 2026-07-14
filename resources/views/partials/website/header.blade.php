@if(Route::is('home', 'company.local.home', 'event.live.home'))
    <nav class="navbar">
        <div class="logo">
            <img src="{{app('event')->logo}}"
                 alt="{{ app('event')->name??'N/A' }}"
                 class="site-logo-img">
        </div>
        <div class="nav-links">
            @if(isset($contents['about-us']) && filled(strip_tags((string) $contents['about-us']->content)))
                <a href="#about-us">About Event</a>
            @endif
            @if(isset($speakers) && $speakers->isNotEmpty())
                <a href="#speakers">Speakers</a>
            @endif
            @if(isset($brands) && $brands->isNotEmpty())
                <a href="#brands">Brands</a>
            @endif
        </div>
        <button class="btn btn-gold" id="openLoginModal">Login</button>
    </nav>
@else
    <div class="main-header">
        <div class="logo">
            <a href="#">
                <img src="{{app('event')->logo}}"
                     alt="{{ app('event')->name??'N/A' }}"
                     class="site-logo-img">
                {{ app('event')->name??'N/A' }}
            </a>
        </div>

        <div class="dropdown user-menu">
            <a href="#" class="me-3">
                <i class="fa-solid fa-bell"></i>
            </a>

            <div class="profile-info position-relative d-flex align-items-center cursor-pointer">
                <img src="{{ auth()->user()->avatar }}"
                     class="rounded-circle me-2"
                     width="38"
                     height="38"
                     alt="User">

                <i class="fa-solid fa-chevron-down small profile-chevron ms-1"></i>

                <div class="profile-dropdown shadow-lg rounded-3">
                    <ul class="list-unstyled mb-0 py-2">
                        <li>
                            <div class="d-flex align-items-center px-3 py-2">
                                <i class="fa-regular fa-user me-3"></i>
                                {{ auth()->user()->name }}
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger px-3 py-2"
                               href="{{ event_route('logout') }}">
                                <i class="fa-solid fa-right-from-bracket me-3"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
