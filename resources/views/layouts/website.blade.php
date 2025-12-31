<!DOCTYPE html>
<html lang="en">

@include('partials.Website.header')

<body>

@yield('body')

@include('partials.Website.footer')

@stack('scripts')
<script>
    window.sliderData = [
            @foreach($banners as $banner)
        {
            type: "{{ $banner->type }}",
            src: "{{ asset('storage/banners/' . $banner->filename) }}"
        }@if(!$loop->last),@endif
        @endforeach
    ];
</script>


<script src="{{ asset('assets/js/Website/script.js') }}"></script>
</body>
</html>
