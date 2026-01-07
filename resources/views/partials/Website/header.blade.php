<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ siteSetting('site_name', config('app.name')) }}
    </title>
    @php
        $favicon = siteSetting('Favicon');
    @endphp

    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/site_settings/' . $favicon) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/site_settings/' . $favicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/site_settings/' . $favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/Website/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/Website/media.css') }}" />

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    />

    @stack('styles')
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
</head>
