<!DOCTYPE html>
<html lang="en">

@include('partials.Website.header')

<body>

@yield('body')

@include('partials.Website.footer')

@stack('scripts')


<script src="{{ asset('assets/js/FormValidator.js') }}"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ asset('assets/js/Website/script.js') }}"></script>
<script src="{{ asset('assets/js/Website/home.js') }}"></script>
</body>
</html>
