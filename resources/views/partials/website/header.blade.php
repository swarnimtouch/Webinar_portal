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
                 class="site-logo-img"
                 style="width:50px; height:50px; object-fit:contain;">


        </div>
        <div class="nav-links">
            <a href="#">About Event</a>
            <a href="#speakers">Speakers</a>
            <a href="#brands">Brands</a>
        </div>
        <button class="btn btn-gold" id="openLoginModal">Login</button>
    </nav>
@else
    {{-- ✅ Home nahi hai to header show karo --}}
    <div class="main-header">
        <div class="logo">
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#3b82f6" width="24" height="24">
                    <path
                        d="M192 32c0-17.7 14.3-32 32-32C383.1 0 512 128.9 512 288c0 17.7-14.3 32-32 32s-32-14.3-32-32C448 164.3 347.7 64 224 64c-17.7 0-32-14.3-32-32zM60.6 220.6L164.7 324.7l28.4-28.4c-.7-2.6-1.1-5.4-1.1-8.3c0-17.7 14.3-32 32-32s32 14.3 32 32s-14.3 32-32 32c-2.9 0-5.6-.4-8.3-1.1l-28.4 28.4L291.4 451.4c14.5 14.5 11.8 38.8-7.3 46.3C260.5 506.9 234.9 512 208 512C93.1 512 0 418.9 0 304c0-26.9 5.1-52.5 14.4-76.1c7.5-19 31.8-21.8 46.3-7.3zM224 96c106 0 192 86 192 192c0 17.7-14.3 32-32 32s-32-14.3-32-32c0-70.7-57.3-128-128-128c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/>
                </svg>
                {{ site_name }}
            </a>
        </div>

        <nav class="main-nav">
            <a href="#"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
            <a href="#" class="active"><i class="fa-solid fa-video"></i> Webinars</a>
            <a href="#"><i class="fa-solid fa-book"></i> Resources</a>
        </nav>

        <div class="dropdown user-menu">
            {{-- 🔔 Bell --}}
            <a href="#" class="me-3">
                <i class="fa-solid fa-bell"></i>
            </a>

            {{-- ⚙️ Gear --}}
            <a href="#" class="me-3">
                <i class="fa-solid fa-gear"></i>
            </a>

            {{-- 👤 User Image --}}
            <a href="#" data-bs-toggle="dropdown">
                <img src="{{ asset('website/images/user.png') }}"
                     class="rounded-circle"
                     width="38"
                     height="38"
                     alt="User">
            </a>

            <ul class="dropdown-menu dropdown-menu-end mt-2 shadow">
                <li>
                    <a class="dropdown-item" href="{{ route('website.dashboard') }}">
                        Welcome, {{ auth()->user()->first_name }}
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('website.logout') }}">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>

@endif
