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
</head>
