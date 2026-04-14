<!DOCTYPE html>
<html lang="en">

@include('partials.website.header')

<body class="webinar-app">

@yield('body')
@if(Route::is('home'))
    @include('partials.website.footer')
@endif


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(Route::is('home'))
    <script src="{{ asset('website/js/script.js') }}"></script>

@elseif(Route::is('website.dashboard'))

    <script src="{{ asset('website/js/dashboard.js') }}"></script>

@else

@endif
@stack('scripts')

</body>
</html>
