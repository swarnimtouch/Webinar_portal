@if(Route::is('home'))
    <nav class="navbar">
        <div class="logo">
            <img src="{{app('event')->logo}}"
                 alt="{{ app('event')->name??'N/A' }}"
                 class="site-logo-img">
        </div>
        <div class="nav-links">
            <a href="#">About Event</a>
            <a href="#speakers">Speakers</a>
            <a href="#brands">Brands</a>
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

        <nav class="main-nav">
            <a href="#"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
            <a href="#" class="active"><i class="fa-solid fa-video"></i> Webinars</a>
            <a href="#"><i class="fa-solid fa-book"></i> Resources</a>
        </nav>

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
                               href="{{ route('logout',['slug'=>request()->route('slug')]) }}">
                                <i class="fa-solid fa-right-from-bracket me-3"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
