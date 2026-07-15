<!DOCTYPE html>
<html lang="en">
<head>
    <base href="">
    <title>{{$title.' | '.site_name}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ Favicon }}"/>
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>
    <!--end::Fonts-->
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
          type="text/css"/>


    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('assets/css/viewbox.css')}}">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css"/>
    <!--end::Global Stylesheets Bundle-->

    @stack('styles')
</head>

<body id="kt_body"
      class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
      style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">

<div class="d-flex flex-column flex-root">
    @yield('body')
</div>

<!-- Metronic Global Scripts -->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{asset('assets/js/viewbox.min.js')}}"></script>
<script type="application/javascript">
    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
    toastr.error("{{ session('error') }}");
    @endif
    $(document).on('click', '.image-link', function (e) {
        e.preventDefault();
        if (!$(this).data('viewbox-initialized')) {
            $(this).viewbox({
                margin: 150,
                closeButton: true
            });
            $(this).data('viewbox-initialized', true);
        }

        $(this).trigger('click');
    });

    // DataTable action menus can be clipped below the card when only one or
    // a few rows are present. Open the last row (or low viewport rows) upward
    // across every admin listing.
    document.addEventListener('show.bs.dropdown', function (event) {
        const toggle = event.target.closest?.('[data-bs-toggle="dropdown"]');
        const row = toggle?.closest('tbody tr');

        if (!toggle || !row) return;

        const wrapper = toggle.closest('.dropdown, .dropup') || toggle.parentElement;
        const isLastVisibleRow = row === row.parentElement?.lastElementChild;
        const availableBelow = window.innerHeight - toggle.getBoundingClientRect().bottom;
        const shouldOpenUp = isLastVisibleRow || availableBelow < 180;

        wrapper?.classList.toggle('dropup', shouldOpenUp);
        toggle.setAttribute('data-bs-boundary', 'viewport');
    });
</script>
@stack('scripts')
</body>
</html>
