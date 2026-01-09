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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    @stack('styles')
<style>
    .error-text {
        margin-top: -14px;
        color: red;
        font-size: 14px;
    }
    .select2-container--default .select2-selection--single{
    border: none;
    }
</style>
</head>
