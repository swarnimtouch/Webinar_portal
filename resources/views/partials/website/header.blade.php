<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title.' | '.site_name}}</title>


    @php $favicon = siteSetting('Favicon'); @endphp
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/site_settings/' . $favicon) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/site_settings/' . $favicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/site_settings/' . $favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @if(Route::is('home'))
        <link rel="stylesheet" href="{{ asset('website/css/style.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('website/css/dashboard.css') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    @stack('styles')
</head>
@if(Route::is('home'))

    <nav class="navbar">
        <div class="logo">
            <img src="{{site_logo}}"
                 alt="{{ site_name }}"
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
                <img src="{{site_logo}}"
                     alt="{{ site_name }}"
                     class="site-logo-img">
                {{ site_name }}
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

            <a href="#" class="me-3">
                <i class="fa-solid fa-gear"></i>
            </a>

            <div class="profile-info position-relative d-flex align-items-center cursor-pointer">
                <img src="{{ asset('website/images/user.png') }}"
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
                                {{ auth()->user()->first_name }}
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger px-3 py-2" href="{{ route('website.logout') }}">
                                <i class="fa-solid fa-right-from-bracket me-3"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endif
